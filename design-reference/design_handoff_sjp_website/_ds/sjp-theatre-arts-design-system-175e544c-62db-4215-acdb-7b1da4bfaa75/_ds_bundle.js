/* @ds-bundle: {"format":4,"namespace":"SJPTheatreArtsDesignSystem_175e54","components":[{"name":"Button","sourcePath":"components/button/Button.jsx"},{"name":"StatusBadge","sourcePath":"components/status-badge/StatusBadge.jsx"}],"sourceHashes":{"components/button/Button.jsx":"236de53557b6","components/status-badge/StatusBadge.jsx":"778d10d5b7b8","ui_kits/studio-manager/ActivityDiscovery.jsx":"12a8f0871d6b","ui_kits/studio-manager/BookingReview.jsx":"ea0bb6e3347d","ui_kits/studio-manager/Checkout.jsx":"20e69595620c","ui_kits/studio-manager/FilterDrawer.jsx":"366842128363","ui_kits/studio-manager/MyAccount.jsx":"815656b150cf","ui_kits/studio-manager/PackageComparison.jsx":"ea48f07ed8a6","ui_kits/studio-manager/SharedUI.jsx":"64d0ab62c275","ui_kits/studio-manager/StudentProfilePopup.jsx":"18754f2c32a4","ui_kits/studio-manager/TeacherRegister.jsx":"16bbb46afb2c"},"inlinedExternals":[],"unexposedExports":[]} */

(() => {

const __ds_ns = (window.SJPTheatreArtsDesignSystem_175e54 = window.SJPTheatreArtsDesignSystem_175e54 || {});

const __ds_scope = {};

(__ds_ns.__errors = __ds_ns.__errors || []);

// components/button/Button.jsx
try { (() => {
// figma node: 8:38 Button (18 variants)
const __venc = v => String(v).replace(/[%|=]/g, encodeURIComponent);
const __vkey = p => "size=" + __venc(p.size) + '|' + "style2=" + __venc(p.style2) + '|' + "state=" + __venc(p.state);
function Button(_p = {}) {
  const props = {
    ..._p,
    label: _p.label ?? "Continue",
    size: _p.size ?? "compact",
    style2: _p.style2 ?? "primary",
    state: _p.state ?? "default"
  };
  const __body0 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-primary)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body1 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-primary)",
      boxShadow: "0 0 0 3px var(--color-brand-focus)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body2 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      opacity: 0.72,
      borderRadius: 12,
      backgroundColor: "var(--color-bg-subtle)",
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-muted)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body3 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-secondary)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body4 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-secondary)",
      boxShadow: "0 0 0 3px var(--color-brand-focus)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body5 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-bg-surface)",
      boxShadow: "inset 0 0 0 2px var(--color-brand-focus)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-brand)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body6 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-min) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-bg-surface)",
      boxShadow: "0 0 0 3px rgb(128,128,128)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 16px 12px 16px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-16) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-16) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-brand)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.label));
  const __body7 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-primary)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0
    }
  }, props.label));
  const __body8 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-primary)",
      boxShadow: "0 0 0 3px var(--color-brand-focus)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0
    }
  }, props.label));
  const __body9 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      opacity: 0.72,
      borderRadius: 12,
      backgroundColor: "var(--color-bg-subtle)",
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-muted)",
      flexShrink: 0
    }
  }, props.label));
  const __body10 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-secondary)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0
    }
  }, props.label));
  const __body11 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-brand-secondary)",
      boxShadow: "0 0 0 3px var(--color-brand-focus)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-inverse)",
      flexShrink: 0
    }
  }, props.label));
  const __body12 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-bg-surface)",
      boxShadow: "inset 0 0 0 2px var(--color-brand-focus)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-brand)",
      flexShrink: 0
    }
  }, props.label));
  const __body13 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      height: "calc(var(--size-control-primary) * 1px)",
      borderRadius: 12,
      backgroundColor: "var(--color-bg-surface)",
      boxShadow: "0 0 0 3px rgb(128,128,128)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "12px 20px 12px 20px",
      justifyContent: "center",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-20) * 1px)",
      paddingTop: "calc(var(--spacing-12) * 1px)",
      paddingRight: "calc(var(--spacing-20) * 1px)",
      paddingBottom: "calc(var(--spacing-12) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 12,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-text-brand)",
      flexShrink: 0
    }
  }, props.label));
  const __impls = {
    // figma: Size=Compact, Style=Primary, State=Default
    "size=compact|style2=primary|state=default": __body0,
    // figma: Size=Compact, Style=Primary, State=Focus
    "size=compact|style2=primary|state=focus": __body1,
    // figma: Size=Compact, Style=Primary, State=Disabled
    "size=compact|style2=primary|state=disabled": __body2,
    // figma: Size=Compact, Style=Secondary, State=Default
    "size=compact|style2=secondary|state=default": __body3,
    // figma: Size=Compact, Style=Secondary, State=Focus
    "size=compact|style2=secondary|state=focus": __body4,
    // figma: Size=Compact, Style=Secondary, State=Disabled
    "size=compact|style2=secondary|state=disabled": __body2,
    // figma: Size=Compact, Style=Outline, State=Default
    "size=compact|style2=outline|state=default": __body5,
    // figma: Size=Compact, Style=Outline, State=Focus
    "size=compact|style2=outline|state=focus": __body6,
    // figma: Size=Compact, Style=Outline, State=Disabled
    "size=compact|style2=outline|state=disabled": __body2,
    // figma: Size=Mobile, Style=Primary, State=Default
    "size=mobile|style2=primary|state=default": __body7,
    // figma: Size=Mobile, Style=Primary, State=Focus
    "size=mobile|style2=primary|state=focus": __body8,
    // figma: Size=Mobile, Style=Primary, State=Disabled
    "size=mobile|style2=primary|state=disabled": __body9,
    // figma: Size=Mobile, Style=Secondary, State=Default
    "size=mobile|style2=secondary|state=default": __body10,
    // figma: Size=Mobile, Style=Secondary, State=Focus
    "size=mobile|style2=secondary|state=focus": __body11,
    // figma: Size=Mobile, Style=Secondary, State=Disabled
    "size=mobile|style2=secondary|state=disabled": __body9,
    // figma: Size=Mobile, Style=Outline, State=Default
    "size=mobile|style2=outline|state=default": __body12,
    // figma: Size=Mobile, Style=Outline, State=Focus
    "size=mobile|style2=outline|state=focus": __body13,
    // figma: Size=Mobile, Style=Outline, State=Disabled
    "size=mobile|style2=outline|state=disabled": __body9
  };
  return (__impls[__vkey(props)] ?? __body0)();
}
Object.assign(__ds_scope, { Button, __ds_default_components_button_Button_15323s1: Button });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/button/Button.jsx", error: String((e && e.message) || e) }); }

// components/status-badge/StatusBadge.jsx
try { (() => {
// figma node: 9:22 Status Badge (5 variants)
const __venc = v => String(v).replace(/[%|=]/g, encodeURIComponent);
const __vkey = p => "severity=" + __venc(p.severity);
function StatusBadge(_p = {}) {
  const props = {
    ..._p,
    severity: _p.severity ?? "success"
  };
  const __body0 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      borderRadius: 999,
      backgroundColor: "var(--color-status-success-bg)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "8px 12px 8px 12px",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-12) * 1px)",
      paddingTop: "calc(var(--spacing-8) * 1px)",
      paddingRight: "calc(var(--spacing-12) * 1px)",
      paddingBottom: "calc(var(--spacing-8) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-success-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text1 ?? "OK"), /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-success-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text2 ?? "Confirmed"));
  const __body1 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      borderRadius: 999,
      backgroundColor: "var(--color-status-warning-bg)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "8px 12px 8px 12px",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-12) * 1px)",
      paddingTop: "calc(var(--spacing-8) * 1px)",
      paddingRight: "calc(var(--spacing-12) * 1px)",
      paddingBottom: "calc(var(--spacing-8) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-warning-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text1 ?? "!"), /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-warning-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text2 ?? "Hold expiring"));
  const __body2 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      borderRadius: 999,
      backgroundColor: "var(--color-status-danger-bg)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "8px 12px 8px 12px",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-12) * 1px)",
      paddingTop: "calc(var(--spacing-8) * 1px)",
      paddingRight: "calc(var(--spacing-12) * 1px)",
      paddingBottom: "calc(var(--spacing-8) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-danger-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text1 ?? "×"), /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-danger-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text2 ?? "Unavailable"));
  const __body3 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      borderRadius: 999,
      backgroundColor: "var(--color-status-info-bg)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "8px 12px 8px 12px",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-12) * 1px)",
      paddingTop: "calc(var(--spacing-8) * 1px)",
      paddingRight: "calc(var(--spacing-12) * 1px)",
      paddingBottom: "calc(var(--spacing-8) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-info-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text1 ?? "i"), /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-info-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text2 ?? "Transferred"));
  const __body4 = () => /*#__PURE__*/React.createElement("div", {
    className: props.className,
    style: {
      width: "fit-content",
      borderRadius: 999,
      backgroundColor: "var(--color-status-neutral-bg)",
      display: "flex",
      flexDirection: "row",
      gap: "calc(var(--spacing-8) * 1px)",
      padding: "8px 12px 8px 12px",
      alignItems: "center",
      flexWrap: "nowrap",
      boxSizing: "border-box",
      paddingLeft: "calc(var(--spacing-12) * 1px)",
      paddingTop: "calc(var(--spacing-8) * 1px)",
      paddingRight: "calc(var(--spacing-12) * 1px)",
      paddingBottom: "calc(var(--spacing-8) * 1px)",
      position: "relative",
      ...props.style
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-neutral-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text1 ?? "•"), /*#__PURE__*/React.createElement("span", {
    style: {
      position: "relative",
      fontFamily: "Montserrat, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif",
      fontWeight: 600,
      fontSize: 13,
      whiteSpace: "nowrap",
      lineHeight: "100%",
      color: "var(--color-status-neutral-fg)",
      flexShrink: 0,
      alignSelf: "stretch"
    }
  }, props.text2 ?? "Cancelled"));
  const __impls = {
    // figma: Severity=Success
    "severity=success": __body0,
    // figma: Severity=Warning
    "severity=warning": __body1,
    // figma: Severity=Danger
    "severity=danger": __body2,
    // figma: Severity=Info
    "severity=info": __body3,
    // figma: Severity=Neutral
    "severity=neutral": __body4
  };
  return (__impls[__vkey(props)] ?? __body0)();
}
Object.assign(__ds_scope, { StatusBadge, __ds_default_components_status_badge_StatusBadge_17kh7ms: StatusBadge });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/status-badge/StatusBadge.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/ActivityDiscovery.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
const {
  SjpHeader,
  BookingFor
} = window;
function DayChip({
  label,
  active
}) {
  return /*#__PURE__*/React.createElement("span", {
    style: {
      padding: "6px 12px",
      borderRadius: 999,
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      background: active ? "var(--color-brand-primary)" : "var(--color-bg-subtle)",
      color: active ? "#fff" : "var(--color-text-primary)"
    }
  }, label);
}
function ActivityCard({
  title,
  meta,
  note,
  noteColor,
  action,
  image
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 14,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      display: "flex",
      flexDirection: "column",
      gap: 8,
      padding: 14
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: 12,
      alignItems: "center"
    }
  }, image && /*#__PURE__*/React.createElement("div", {
    style: {
      width: 60,
      height: 60,
      borderRadius: 10,
      background: "var(--brand-orange-50)",
      flexShrink: 0,
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      fontSize: 22
    }
  }, "\uD83E\uDE70"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: 3
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 16,
      color: "var(--color-brand-secondary)"
    }
  }, title), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, meta), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: noteColor
    }
  }, note))), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "outline",
    label: action
  }));
}
function ActivityDiscovery({
  onNext,
  onOpenFilters
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: "100%",
      display: "flex",
      flexDirection: "column",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: 14,
      padding: "0 20px 16px",
      overflow: "hidden"
    }
  }, /*#__PURE__*/React.createElement(SjpHeader, null), /*#__PURE__*/React.createElement("div", {
    style: {
      height: 10
    }
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "STEP 1 OF 4 \xA0\u2022\xA0 CHOOSE A CLASS"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 25,
      color: "var(--color-brand-secondary)"
    }
  }, "Find the right class for Ava"), /*#__PURE__*/React.createElement(BookingFor, {
    name: "Ava Williams \u2022 age 8"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement(DayChip, {
    label: "Mon"
  }), /*#__PURE__*/React.createElement(DayChip, {
    label: "Tue",
    active: true
  }), /*#__PURE__*/React.createElement(DayChip, {
    label: "Sat"
  }), /*#__PURE__*/React.createElement("span", {
    onClick: onOpenFilters,
    style: {
      cursor: "pointer",
      padding: "6px 12px",
      borderRadius: 999,
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      background: "var(--brand-orange-50)",
      color: "var(--color-brand-primary)"
    }
  }, "Filters (2)")), /*#__PURE__*/React.createElement(ActivityCard, {
    title: "Ballet Fundamentals",
    meta: "Tuesday \u2022 4:15\u20135:00pm \u2022 Studio 1",
    note: "\u2713 5 spaces \u2022 Great for Ava's age",
    noteColor: "var(--color-status-success-fg)",
    action: "View options",
    image: true
  }), /*#__PURE__*/React.createElement(ActivityCard, {
    title: "Acro Skills",
    meta: "Saturday \u2022 10:00\u201310:45am \u2022 Studio 2",
    note: "! Overlaps with Junior Tap",
    noteColor: "var(--color-status-warning-fg)",
    action: "Check another time"
  })), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "1px solid var(--color-border-default)",
      padding: "14px 20px 16px",
      display: "flex",
      flexDirection: "column",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Showing classes suitable for Ava"), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: "Review selected class",
    onClick: onNext
  })));
}
window.ActivityDiscovery = ActivityDiscovery;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/ActivityDiscovery.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/BookingReview.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function SummaryRow({
  label,
  value
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, label), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-brand-secondary)",
      whiteSpace: "pre-line"
    }
  }, value));
}
function BookingReview({
  onBack,
  onNext,
  package: pkg
}) {
  const isFull = pkg !== "single";
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: "100%",
      display: "flex",
      flexDirection: "column",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: 12,
      padding: "18px 20px 16px"
    }
  }, /*#__PURE__*/React.createElement("span", {
    onClick: onBack,
    style: {
      cursor: "pointer",
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-primary)"
    }
  }, "\u2039 Back"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "STEP 3 OF 4 \xA0\u2022\xA0 REVIEW BOOKING"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 20,
      color: "var(--color-brand-secondary)"
    }
  }, "Review Ava's booking"), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 8,
      background: "var(--brand-orange-50)",
      padding: "0 12px",
      height: 38,
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-brand-secondary)"
    }
  }, "Booking for Ava Williams"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-primary)"
    }
  }, "Change")), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: 14,
      display: "flex",
      flexDirection: "column",
      gap: 12
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 16,
      color: "var(--color-brand-secondary)"
    }
  }, "Ballet Fundamentals"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Tuesdays \u2022 4:15\u20135:00pm \u2022 Studio 1"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-status-success-fg)"
    }
  }, "\u2713 ", isFull ? "8 dates available • No overlaps" : "1 date available • No overlaps")), /*#__PURE__*/React.createElement(SummaryRow, {
    label: "Dates",
    value: isFull ? "8 Tuesdays\nView all dates" : "1 Tuesday"
  }), /*#__PURE__*/React.createElement(SummaryRow, {
    label: "Standard rate",
    value: "\xA313"
  }), isFull && /*#__PURE__*/React.createElement(SummaryRow, {
    label: "Package rate",
    value: "\xA312"
  }), isFull && /*#__PURE__*/React.createElement(SummaryRow, {
    label: "Saving",
    value: "\xA38"
  }), /*#__PURE__*/React.createElement(SummaryRow, {
    label: "Total due",
    value: isFull ? "£96.00" : "£13.00"
  })), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 8,
      background: "var(--color-status-info-bg)",
      padding: "10px 12px"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-status-info-fg)"
    }
  }, "Check the dates and total before checkout. You can go back without losing your place."))), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "1px solid var(--color-border-default)",
      padding: "13px 20px 9px",
      display: "flex",
      flexDirection: "column",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Due today"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 17,
      color: "var(--color-brand-secondary)"
    }
  }, isFull ? "£96.00" : "£13.00")), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: "Continue to checkout",
    onClick: onNext
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 10,
      color: "var(--color-text-muted)"
    }
  }, "Your place remains held while you complete checkout.")));
}
window.BookingReview = BookingReview;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/BookingReview.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/Checkout.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function InfoRow({
  label,
  value
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 10,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: "11px 12px",
      display: "flex",
      flexDirection: "column",
      gap: 3
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 10,
      color: "var(--color-text-muted)"
    }
  }, label), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, value));
}
function Checkout({
  onBack,
  onNext,
  useCredit,
  onToggleCredit
}) {
  const total = 96,
    credit = useCredit ? 25 : 0,
    due = total - credit;
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: "100%",
      display: "flex",
      flexDirection: "column",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: 12,
      padding: "18px 20px"
    }
  }, /*#__PURE__*/React.createElement("span", {
    onClick: onBack,
    style: {
      cursor: "pointer",
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-primary)"
    }
  }, "\u2039 Back"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "STEP 4 OF 4 \xA0\u2022\xA0 CHECKOUT"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 20,
      color: "var(--color-brand-secondary)"
    }
  }, "Ready to book Ava's place"), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--status-amber-50)",
      padding: 12,
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--status-amber-800)"
    }
  }, "! Place held while you pay"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 16,
      color: "var(--status-amber-800)"
    }
  }, "09:42")), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 14,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: 14,
      display: "flex",
      flexDirection: "column",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 15,
      color: "var(--color-brand-secondary)"
    }
  }, "Ava Williams"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Ballet Fundamentals \u2022 Full term"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-muted)"
    }
  }, "8 Tuesdays \u2022 10 Sep\u201329 Oct \u2022 4:15pm"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Package total"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 16,
      color: "var(--color-brand-secondary)"
    }
  }, "\xA396.00"))), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--color-status-success-bg)",
      padding: 12,
      display: "flex",
      flexDirection: "column",
      gap: 7
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-status-success-fg)"
    }
  }, "\u2713 Use \xA325 account credit"), /*#__PURE__*/React.createElement("span", {
    onClick: onToggleCredit,
    style: {
      cursor: "pointer",
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 10,
      color: "var(--color-status-success-fg)"
    }
  }, useCredit ? "ON" : "OFF")), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 10,
      color: "var(--color-status-success-fg)"
    }
  }, "\xA3", 25 - (useCredit ? 25 - 7 : 0) === 7 ? 7 : 25, " credit will remain after this booking.")), /*#__PURE__*/React.createElement(InfoRow, {
    label: "Contact details",
    value: "gareth@example.com"
  }), /*#__PURE__*/React.createElement(InfoRow, {
    label: "Payment",
    value: "Visa ending 4242  \u203A"
  })), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "1px solid var(--color-border-default)",
      padding: "13px 20px 16px",
      display: "flex",
      flexDirection: "column",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Due today", useCredit ? " after credit" : ""), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 18,
      color: "var(--color-brand-secondary)"
    }
  }, "\xA3", due, ".00")), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: `Pay £${due}.00 and confirm`,
    onClick: onNext
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 10,
      color: "var(--color-text-muted)"
    }
  }, "You will receive confirmation by email.")));
}
window.Checkout = Checkout;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/Checkout.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/FilterDrawer.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function FilterRow({
  label,
  value
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 10,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: "10px 12px",
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, label), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-muted)"
    }
  }, value));
}
function FilterDrawer({
  onClose
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      position: "absolute",
      inset: 0,
      borderRadius: 20,
      overflow: "hidden",
      display: "flex"
    }
  }, /*#__PURE__*/React.createElement("div", {
    onClick: onClose,
    style: {
      width: 56,
      background: "var(--color-brand-secondary)",
      opacity: 0.28,
      cursor: "pointer"
    }
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      flex: 1,
      background: "#fff",
      padding: "24px 20px 20px",
      display: "flex",
      flexDirection: "column",
      gap: 16
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 20,
      color: "var(--color-brand-secondary)"
    }
  }, "Filter classes"), /*#__PURE__*/React.createElement("span", {
    onClick: onClose,
    style: {
      cursor: "pointer",
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 22,
      color: "var(--color-text-primary)"
    }
  }, "\xD7")), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "var(--color-text-muted)"
    }
  }, "2 filters applied \u2022 results update immediately"), /*#__PURE__*/React.createElement(FilterRow, {
    label: "Term",
    value: "Autumn term"
  }), /*#__PURE__*/React.createElement(FilterRow, {
    label: "Date range",
    value: "10 Sep \u2014 29 Oct"
  }), /*#__PURE__*/React.createElement(FilterRow, {
    label: "Age range",
    value: "7 \u2014 10 years"
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-status-info-fg)"
    }
  }, "Weekday shortcuts stay visible on the class list. Use these filters when you need to narrow further."), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between",
      marginTop: "auto"
    }
  }, /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "outline",
    label: "Clear all"
  }), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: "Show 6 classes",
    onClick: onClose
  }))));
}
window.FilterDrawer = FilterDrawer;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/FilterDrawer.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/MyAccount.jsx
try { (() => {
const {
  StatusBadge,
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function MyAccount({
  participant,
  onSwitch
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: "100%",
      display: "flex",
      flexDirection: "column"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      background: "var(--color-brand-secondary)",
      padding: "20px 20px 18px",
      display: "flex",
      flexDirection: "column",
      gap: 10
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 10,
      color: "#fff"
    }
  }, "SJP THEATRE ARTS \u2022 MY ACCOUNT"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 23,
      color: "#fff"
    }
  }, "Welcome back, Gareth"), /*#__PURE__*/React.createElement("div", {
    style: {
      width: 98,
      borderRadius: 999,
      background: "#fff",
      padding: 4,
      display: "flex"
    }
  }, ["Ava", "Mia"].map(n => /*#__PURE__*/React.createElement("span", {
    key: n,
    onClick: () => onSwitch(n),
    style: {
      cursor: "pointer",
      flex: 1,
      textAlign: "center",
      padding: "7px 0",
      borderRadius: 999,
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      background: participant === n ? "var(--color-brand-primary)" : "transparent",
      color: participant === n ? "#fff" : "var(--color-brand-secondary)"
    }
  }, n)))), /*#__PURE__*/React.createElement("div", {
    style: {
      flex: 1,
      overflow: "auto",
      padding: "16px 20px",
      display: "flex",
      flexDirection: "column",
      gap: 13
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 20,
      color: "var(--color-brand-secondary)"
    }
  }, participant, "'s next booking"), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 14,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: 14,
      display: "flex",
      flexDirection: "column",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 16,
      color: "var(--color-brand-secondary)"
    }
  }, "Ballet Fundamentals"), /*#__PURE__*/React.createElement(StatusBadge, {
    severity: "success",
    text1: "\u2713",
    text2: "Confirmed"
  })), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, "Tuesday 10 Sep \u2022 4:15\u20135:00pm"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "Studio 1 \u2022 Teacher: Miss Rachel"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement(Button, {
    size: "compact",
    style2: "primary",
    label: "Confirm"
  }), /*#__PURE__*/React.createElement(Button, {
    size: "compact",
    style2: "outline",
    label: "Can't attend"
  }))), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--color-status-info-bg)",
      padding: "10px 12px"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-status-info-fg)"
    }
  }, "i Expected absence recorded for 24 Sep")), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: 10
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      flex: 1,
      borderRadius: 12,
      background: "var(--color-status-success-bg)",
      padding: "12px 14px"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 18,
      color: "var(--color-brand-secondary)"
    }
  }, "\xA332"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-text-primary)"
    }
  }, "Credit")), /*#__PURE__*/React.createElement("div", {
    style: {
      flex: 1,
      borderRadius: 12,
      background: "var(--brand-orange-50)",
      padding: "12px 14px"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 18,
      color: "var(--color-brand-secondary)"
    }
  }, "3"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-text-primary)"
    }
  }, "Upcoming"))), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 18,
      color: "var(--color-brand-secondary)"
    }
  }, "Manage"), ["Booking credit  ›", "Reminder preferences  ›", "Participant details  ›"].map(l => /*#__PURE__*/React.createElement("div", {
    key: l,
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 13,
      color: "var(--color-text-primary)",
      padding: "10px 0",
      borderBottom: "1px solid var(--color-border-default)"
    }
  }, l))));
}
window.MyAccount = MyAccount;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/MyAccount.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/PackageComparison.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function PackageOption({
  name,
  price,
  meta,
  detail,
  action,
  recommended,
  onSelect,
  selected
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 14,
      padding: 14,
      display: "flex",
      flexDirection: "column",
      gap: 8,
      background: recommended ? "var(--brand-orange-50)" : "#fff",
      boxShadow: recommended ? "inset 0 0 0 2px var(--color-brand-primary)" : "inset 0 0 0 1px var(--color-border-default)"
    }
  }, recommended && /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 10,
      color: "var(--color-status-success-fg)"
    }
  }, "RECOMMENDED"), /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 16,
      color: "var(--color-brand-secondary)"
    }
  }, name), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 17,
      color: "var(--color-brand-secondary)"
    }
  }, price)), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)",
      whiteSpace: "pre-line"
    }
  }, meta), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, detail), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: recommended ? "primary" : "outline",
    label: selected ? "✓ Selected" : action,
    onClick: onSelect
  }));
}
function PackageComparison({
  onBack,
  onNext,
  selected,
  onSelect
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: "100%",
      display: "flex",
      flexDirection: "column",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: 14,
      padding: "18px 20px"
    }
  }, /*#__PURE__*/React.createElement("span", {
    onClick: onBack,
    style: {
      cursor: "pointer",
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-primary)"
    }
  }, "\u2039 Back"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "STEP 2 OF 4 \xA0\u2022\xA0 CHOOSE A PACKAGE"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 22,
      color: "var(--color-brand-secondary)"
    }
  }, "Choose how Ava will attend"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 13,
      color: "var(--color-text-muted)"
    }
  }, "Ballet Fundamentals \u2022 Tuesdays at 4:15pm"), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--brand-orange-50)",
      padding: "10px 12px",
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-brand-secondary)"
    }
  }, "Booking for Ava Williams"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-primary)"
    }
  }, "Change")), /*#__PURE__*/React.createElement(PackageOption, {
    name: "Full term",
    price: "\xA396",
    meta: "8 Tuesdays • 10 Sep–29 Oct\nView all dates",
    detail: "Best value \u2022 includes all available dates",
    action: "Choose Full term",
    recommended: true,
    selected: selected === "full",
    onSelect: () => onSelect("full")
  }), /*#__PURE__*/React.createElement(PackageOption, {
    name: "Single class",
    price: "\xA313",
    meta: "Choose one Tuesday",
    detail: "Flexible, subject to availability",
    action: "Choose Single class",
    selected: selected === "single",
    onSelect: () => onSelect("single")
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 10,
      background: "var(--color-status-info-bg)",
      padding: "10px 12px",
      display: "flex",
      flexDirection: "column",
      gap: 4
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-status-info-fg)"
    }
  }, "i Joining after term start?"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-status-info-fg)"
    }
  }, "You only pay for the remaining 8 sessions."))), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "1px solid var(--color-border-default)",
      padding: "13px 20px 16px",
      display: "flex",
      flexDirection: "column",
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, selected === "single" ? "Single class • 1 date" : "Full term • 8 dates"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 17,
      color: "var(--color-brand-secondary)"
    }
  }, selected === "single" ? "£13" : "£96")), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: "Continue to review",
    onClick: onNext
  })));
}
window.PackageComparison = PackageComparison;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/PackageComparison.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/SharedUI.jsx
try { (() => {
// Shared chrome for the Guided Confidence mobile kit: phone frame, purple app header, "booking for" context bar.
function Phone({
  children
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      width: 390,
      height: 844,
      borderRadius: 20,
      overflow: "hidden",
      background: "#fff",
      display: "flex",
      flexDirection: "column",
      justifyContent: "space-between",
      boxShadow: "var(--shadow-raised)",
      position: "relative"
    }
  }, children);
}
function SjpHeader({
  eyebrow
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: 56,
      background: "var(--color-brand-secondary)",
      display: "flex",
      padding: "14px 20px",
      justifyContent: "space-between",
      alignItems: "center",
      flexShrink: 0
    }
  }, /*#__PURE__*/React.createElement("img", {
    src: "../../assets/header-mark.png",
    alt: "SJP Theatre Arts",
    style: {
      height: 28
    }
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 15,
      color: "#fff"
    }
  }, eyebrow || "SJP THEATRE ARTS"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "#fff"
    }
  }, "My account"));
}
function Avatar({
  size = 44
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      width: size,
      height: size,
      borderRadius: 999,
      background: "var(--color-bg-subtle)",
      flexShrink: 0,
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      fontSize: size * 0.4,
      color: "var(--color-text-muted)"
    }
  }, "\uD83D\uDC64");
}
function BookingFor({
  name
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: 72,
      borderRadius: 14,
      background: "var(--brand-orange-50)",
      display: "flex",
      gap: 9,
      padding: 14,
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement(Avatar, null), /*#__PURE__*/React.createElement("div", {
    style: {
      flex: 1,
      display: "flex",
      flexDirection: "column",
      gap: 2
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "Booking for"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 13,
      color: "var(--color-brand-secondary)"
    }
  }, name)), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-primary)"
    }
  }, "Change"));
}
function StickyBar({
  summary,
  children
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "1px solid var(--color-border-default)",
      padding: "13px 20px 16px",
      display: "flex",
      flexDirection: "column",
      gap: 8,
      flexShrink: 0
    }
  }, summary && /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--color-text-primary)"
    }
  }, summary), children);
}
Object.assign(window, {
  Phone,
  SjpHeader,
  Avatar,
  BookingFor,
  StickyBar
});
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/SharedUI.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/StudentProfilePopup.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function ProfileSection({
  title,
  detail
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 14,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: "12px 14px",
      display: "flex",
      flexDirection: "column",
      gap: 4
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 14,
      color: "var(--color-brand-secondary)"
    }
  }, title), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-text-primary)"
    }
  }, detail));
}
function StudentProfilePopup({
  onClose
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      position: "absolute",
      inset: 0,
      borderRadius: 20,
      background: "rgba(24,12,36,0.48)",
      display: "flex",
      alignItems: "center",
      justifyContent: "center"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 350,
      borderRadius: 24,
      background: "#fff",
      boxShadow: "var(--shadow-raised)",
      padding: 20,
      display: "flex",
      flexDirection: "column",
      gap: 14
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      gap: 10,
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 44,
      height: 44,
      borderRadius: 999,
      background: "var(--color-bg-subtle)",
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      fontSize: 18
    }
  }, "\uD83D\uDC64"), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 18,
      color: "var(--color-brand-secondary)"
    }
  }, "Ava Williams"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 11,
      color: "var(--color-text-muted)"
    }
  }, "Age 8 \xB7 Student"))), /*#__PURE__*/React.createElement("span", {
    onClick: onClose,
    style: {
      cursor: "pointer",
      width: 36,
      height: 36,
      borderRadius: 999,
      background: "var(--color-bg-subtle)",
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 18,
      color: "var(--color-brand-secondary)"
    }
  }, "\xD7")), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--color-status-success-bg)",
      padding: 12
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-status-success-fg)"
    }
  }, "\u2713 Protected access active \xB7 5 minutes")), /*#__PURE__*/React.createElement(ProfileSection, {
    title: "Emergency contact",
    detail: "Gareth Williams \xB7 Dad \xB7 07700 900 123"
  }), /*#__PURE__*/React.createElement(ProfileSection, {
    title: "Medical conditions",
    detail: "Mild asthma \xB7 inhaler held in the front office."
  }), /*#__PURE__*/React.createElement(ProfileSection, {
    title: "Collection notes",
    detail: "Authorised: Gareth Williams and Emma Jones."
  }), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: "Close profile",
    onClick: onClose
  })));
}
window.StudentProfilePopup = StudentProfilePopup;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/StudentProfilePopup.jsx", error: String((e && e.message) || e) }); }

// ui_kits/studio-manager/TeacherRegister.jsx
try { (() => {
const {
  Button
} = window.SJPTheatreArtsDesignSystem_175e54;
function StudentRow({
  name,
  status,
  onOpen
}) {
  return /*#__PURE__*/React.createElement("div", {
    onClick: onOpen,
    style: {
      cursor: "pointer",
      borderRadius: 12,
      boxShadow: "inset 0 0 0 1px var(--color-border-default)",
      padding: "10px 12px",
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 13,
      color: "var(--color-brand-secondary)"
    }
  }, name), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: status ? "var(--color-status-success-fg)" : "var(--color-text-muted)"
    }
  }, status || "Not marked"));
}
function TeacherRegister({
  onOpenProfile
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      height: "100%",
      display: "flex",
      flexDirection: "column",
      justifyContent: "space-between"
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: "flex",
      flexDirection: "column",
      gap: 12
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      background: "var(--color-brand-secondary)",
      padding: "18px 20px 16px",
      display: "flex",
      flexDirection: "column",
      gap: 5
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 600,
      fontSize: 15,
      color: "#fff"
    }
  }, "Ballet Fundamentals"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 12,
      color: "#fff"
    }
  }, "4:15\u20135:00pm \u2022 Studio 1 \u2022 8 students")), /*#__PURE__*/React.createElement("div", {
    style: {
      padding: "0 16px",
      display: "flex",
      flexDirection: "column",
      gap: 10
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--status-amber-50)",
      padding: "10px 12px",
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center"
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 12,
      color: "var(--status-amber-800)"
    }
  }, "5 of 8 marked"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Poppins,sans-serif",
      fontWeight: 700,
      fontSize: 15,
      color: "var(--status-amber-800)"
    }
  }, "63%")), /*#__PURE__*/React.createElement(StudentRow, {
    name: "Ben Carter",
    onOpen: onOpenProfile
  }), /*#__PURE__*/React.createElement(StudentRow, {
    name: "Mia Jones",
    status: "\u2713 Expected",
    onOpen: onOpenProfile
  }), /*#__PURE__*/React.createElement(StudentRow, {
    name: "Oliver Smith",
    status: "Present",
    onOpen: onOpenProfile
  }), /*#__PURE__*/React.createElement(StudentRow, {
    name: "Ava Williams",
    onOpen: onOpenProfile
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      borderRadius: 12,
      background: "var(--brand-orange-50)",
      padding: "10px 12px",
      display: "flex",
      flexDirection: "column",
      gap: 3
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--color-brand-secondary)"
    }
  }, "Student profiles are protected"), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontSize: 10,
      color: "var(--color-brand-secondary)"
    }
  }, "Re-enter your password to view emergency and medical details.")))), /*#__PURE__*/React.createElement("div", {
    style: {
      borderTop: "1px solid var(--color-border-default)",
      padding: "13px 20px 16px",
      display: "flex",
      flexDirection: "column",
      gap: 7
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: "Montserrat,sans-serif",
      fontWeight: 600,
      fontSize: 11,
      color: "var(--status-amber-800)"
    }
  }, "3 students still need a status"), /*#__PURE__*/React.createElement(Button, {
    size: "mobile",
    style2: "primary",
    label: "Complete register"
  })));
}
window.TeacherRegister = TeacherRegister;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/studio-manager/TeacherRegister.jsx", error: String((e && e.message) || e) }); }

__ds_ns.Button = __ds_scope.Button;

__ds_ns.StatusBadge = __ds_scope.StatusBadge;

})();
