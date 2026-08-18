#!/usr/bin/env sh
# Rebuild the `production` branch from the current working tree.
#
# `main` carries the design bundle, docs, review tooling and dev config; the
# live site needs none of it. This copies only the runtime files into an
# orphan branch (no shared history, so a stale delete on main can never
# resurface as a merge conflict) and commits them. Run it after every merge
# to main you want live, then push the branch:
#
#   sh tools/build-production.sh && git push origin production
#
# The uncommitted state of the working tree is what ships, so commit main
# first if you want the two to line up.
set -eu

ROOT=$(git rev-parse --show-toplevel)
BRANCH=production
WT="$ROOT/.production-worktree"
SRC=$(git -C "$ROOT" rev-parse --short HEAD)

# Everything WordPress reads. Add here when a new top-level runtime dir appears.
INCLUDE="style.css theme.json functions.php inc blocks patterns parts templates assets acf-json languages data"

cd "$ROOT"
git worktree remove --force "$WT" 2>/dev/null || true
if git show-ref --quiet "refs/heads/$BRANCH"; then
	git worktree add --quiet "$WT" "$BRANCH"
else
	git worktree add --quiet --detach "$WT"
	git -C "$WT" checkout --quiet --orphan "$BRANCH"
fi

# Wipe the worktree (bar .git) and copy the include list in fresh, so deletions
# on main land here too rather than lingering.
find "$WT" -mindepth 1 -maxdepth 1 ! -name .git -exec rm -rf {} +
for item in $INCLUDE; do
	[ -e "$item" ] && cp -R "$item" "$WT/$item"
done
printf '%s\n' '# Runtime files only. Rebuilt from main by tools/build-production.sh; do not edit here.' > "$WT/.gitignore"

git -C "$WT" add -A
if git -C "$WT" diff --cached --quiet; then
	echo "production: nothing changed since last build"
else
	git -C "$WT" commit --quiet -m "Build production from main @ $SRC"
	echo "production: committed build from main @ $SRC"
fi
git worktree remove --force "$WT"
git -C "$ROOT" branch --list "$BRANCH" -v
