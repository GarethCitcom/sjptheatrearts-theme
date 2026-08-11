
function buildRenderer(nc, blobIdx, allBlobs){
  const offs=[]; {let o=0; for(const l of blobIdx){ offs.push(o); o+=l; }}
  function pathFromBlob(i){
    const b = allBlobs.slice(offs[i], offs[i]+blobIdx[i]);
    const dv=new DataView(b.buffer,b.byteOffset,b.byteLength);
    let p=0, d='';
    const rd=()=>{const f=dv.getFloat32(p,true);p+=4;return Math.round(f*100)/100;};
    while(p<b.length){ const c=b[p++];
      if(c===0){} else if(c===1){ d+='M'+rd()+' '+rd(); }
      else if(c===2){ d+='L'+rd()+' '+rd(); }
      else if(c===3){ d+='Q'+rd()+' '+rd()+' '+rd()+' '+rd(); }
      else if(c===4){ d+='C'+rd()+' '+rd()+' '+rd()+' '+rd()+' '+rd()+' '+rd(); }
      else if(c===5){ d+='Z'; } else return d; }
    return d;
  }
  const key=g=>g?g.sessionID+':'+g.localID:null;
  const kids={}; for(const n of nc){ const pk=key(n.parentIndex&&n.parentIndex.guid); if(pk==null) continue; (kids[pk]=kids[pk]||[]).push(n); }
  for(const k in kids) kids[k].sort((a,b)=> a.parentIndex.position < b.parentIndex.position ? -1 : 1);
  const col=c=>c?'rgba('+Math.round(c.r*255)+','+Math.round(c.g*255)+','+Math.round(c.b*255)+','+(+(c.a==null?1:c.a).toFixed(3))+')':'transparent';
  function paintCss(p){
    if(!p||p.visible===false) return null;
    const op = p.opacity==null?1:p.opacity;
    if(p.type==='SOLID'){ return col({...p.color, a:(p.color.a==null?1:p.color.a)*op}); }
    if(p.type&&p.type.startsWith('GRADIENT')){
      const stops=(p.stops||[]).map(s=>col({...s.color,a:(s.color.a==null?1:s.color.a)*op})+' '+Math.round(s.position*100)+'%').join(',');
      const t=p.transform||{m00:1,m10:0};
      if(p.type!=='GRADIENT_LINEAR') return 'radial-gradient('+stops+')';
      return 'linear-gradient('+Math.round(Math.atan2(t.m10,t.m00)*180/Math.PI+90)+'deg,'+stops+')';
    }
    if(p.type==='IMAGE') return 'repeating-linear-gradient(45deg,#d8d8d8,#d8d8d8 10px,#e9e9e9 10px,#e9e9e9 20px)';
    return null;
  }
  const esc=s=>String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  const fonts=new Set();
  function render(n, depth, opts){
    opts=opts||{};
    if(n.visible===false) return '';
    const s=n.size||{x:0,y:0}, t=n.transform||{m00:1,m01:0,m02:0,m10:0,m11:1,m12:0};
    const w=s.x,h=s.y;
    let st='position:absolute;left:0;top:0;width:'+w+'px;height:'+h+'px;transform:matrix('+t.m00+','+t.m10+','+t.m01+','+t.m11+','+t.m02+','+t.m12+');transform-origin:0 0;';
    if(n.opacity!=null&&n.opacity<1) st+='opacity:'+n.opacity+';';
    if(n.rectangleCornerRadiiIndependent) st+='border-radius:'+(n.rectangleTopLeftCornerRadius||0)+'px '+(n.rectangleTopRightCornerRadius||0)+'px '+(n.rectangleBottomRightCornerRadius||0)+'px '+(n.rectangleBottomLeftCornerRadius||0)+'px;';
    else if(n.cornerRadius) st+='border-radius:'+n.cornerRadius+'px;';
    if(n.type==='ELLIPSE') st+='border-radius:50%;';
    const isVec = n.type==='VECTOR'||n.type==='BOOLEAN_OPERATION'||n.type==='LINE'||n.type==='STAR'||n.type==='REGULAR_POLYGON';
    let inner='';
    if(!isVec && n.type!=='TEXT'){
      const bgs=(n.fillPaints||[]).filter(p=>p.visible!==false).map(paintCss).filter(Boolean).reverse();
      if(bgs.length) st+='background:'+bgs.map(b=>b.startsWith('rgba')?'linear-gradient('+b+','+b+')':b).join(',')+';';
    }
    const sp=(n.strokePaints||[]).find(p=>p.visible!==false&&p.type==='SOLID');
    if(sp&&n.strokeWeight) st+='box-sizing:border-box;border:'+n.strokeWeight+'px solid '+paintCss(sp)+';';
    const sh=(n.effects||[]).filter(e=>e.visible!==false&&(e.type==='DROP_SHADOW'||e.type==='INNER_SHADOW')).map(e=>(e.type==='INNER_SHADOW'?'inset ':'')+e.offset.x+'px '+e.offset.y+'px '+e.radius+'px '+(e.spread||0)+'px '+col(e.color));
    if(sh.length) st+='box-shadow:'+sh.join(',')+';';
    if(n.type==='FRAME'&&n.frameMaskDisabled===false) st+='overflow:hidden;';
    if(isVec && !opts.noVec){
      const geo=(n.fillGeometry&&n.fillGeometry.length?n.fillGeometry:n.strokeGeometry)||[];
      const fp=(n.fillPaints||[]).find(p=>p.visible!==false)||(n.strokePaints||[]).find(p=>p.visible!==false);
      const fill=fp?paintCss(fp):'#999';
      const ds=geo.map(g=>pathFromBlob(g.commandsBlob)).filter(Boolean);
      if(ds.length) inner='<svg width="'+w+'" height="'+h+'" viewBox="0 0 '+w+' '+h+'" style="position:absolute;left:0;top:0;overflow:visible">'+ds.map(d=>'<path d="'+d+'" fill="'+fill+'"/>').join('')+'</svg>';
    }
    if(n.type==='TEXT'&&n.textData){
      const f=n.fontName||{family:'Inter',style:'Regular'}; fonts.add(f.family);
      const fw=/ExtraBold|Black/i.test(f.style)?800:/SemiBold/i.test(f.style)?600:/Bold/i.test(f.style)?700:/Medium/i.test(f.style)?500:/Light/i.test(f.style)?300:400;
      const lh=n.lineHeight?(n.lineHeight.units==='PERCENT'?(n.lineHeight.value/100):n.lineHeight.value+'px'):'normal';
      const fpaint=(n.fillPaints||[]).find(p=>p.visible!==false);
      const align={LEFT:'left',RIGHT:'right',CENTER:'center',JUSTIFIED:'justify'}[n.textAlignHorizontal||'LEFT'];
      const va={TOP:'flex-start',CENTER:'center',BOTTOM:'flex-end'}[n.textAlignVertical||'TOP'];
      st+="font-family:'"+f.family+"',sans-serif;font-weight:"+fw+';font-size:'+(n.fontSize||16)+'px;line-height:'+lh+';color:'+(fpaint?paintCss(fpaint):'#000')+';text-align:'+align+';display:flex;flex-direction:column;justify-content:'+va+';white-space:pre-wrap;';
      if(n.letterSpacing&&n.letterSpacing.value) st+='letter-spacing:'+(n.letterSpacing.units==='PERCENT'?(n.letterSpacing.value/100)+'em':n.letterSpacing.value+'px')+';';
      if(n.textCase==='UPPER') st+='text-transform:uppercase;';
      inner='<div>'+esc(n.textData.characters||'')+'</div>';
    }
    const ch=kids[key(n.guid)]||[];
    return '<div data-n="'+esc(n.name||'')+'" style="'+st+'">'+inner+(depth>50?'':ch.map(c=>render(c,depth+1,opts)).join(''))+'</div>';
  }
  function page(node, scale, opts){
    fonts.clear();
    const body=render(node,0,opts).replace(/transform:matrix\([^)]*\)/,'transform:none');
    const fam=[...fonts].filter(f=>!/Font Awesome/.test(f));
    return '<!DOCTYPE html><html><head><meta charset="utf-8"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?'+fam.map(f=>'family='+encodeURIComponent(f)+':wght@300;400;500;600;700;800').join('&')+'&display=swap"><style>body{margin:0;background:#fff}#w{width:'+Math.round(node.size.x*scale)+'px;height:'+Math.round(node.size.y*scale)+'px;position:relative;overflow:hidden}#s{position:relative;width:'+node.size.x+'px;height:'+node.size.y+'px;transform:scale('+scale+');transform-origin:0 0}</style></head><body><div id="w"><div id="s">'+body+'</div></div></body></html>';
  }
  return {render,page,kids,key,fonts,pathFromBlob};
}
