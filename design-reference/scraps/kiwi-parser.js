
const f32 = new Float32Array(1); const i32 = new Int32Array(f32.buffer);
class BB {
  constructor(d){ this.d=d; this.i=0; }
  byte(){ return this.d[this.i++]; }
  bool(){ return !!this.d[this.i++]; }
  varUint(){ let v=0,s=0,b; do{ b=this.d[this.i++]; v |= (b&127)<<s; s+=7; }while(b&128 && s<35); return v>>>0; }
  varInt(){ const v=this.varUint(); return (v&1)? ~(v>>>1) : (v>>>1); }
  varUint64(){ let v=0n,s=0n,b; do{ b=this.d[this.i++]; v |= BigInt(b&127)<<s; s+=7n; }while(b&128 && s<70n); return v; }
  varInt64(){ const v=this.varUint64(); return (v&1n)? -((v>>1n)+1n) : (v>>1n); }
  float(){ const first=this.d[this.i]; if(first===0){this.i++;return 0;} const d=this.d,i=this.i;
    let bits = (d[i] | (d[i+1]<<8) | (d[i+2]<<16) | (d[i+3]<<24))>>>0; this.i+=4;
    bits = ((bits<<23) | (bits>>>9))>>>0; i32[0]=bits|0; return f32[0]; }
  string(){ const s=this.i; while(this.d[this.i]!==0) this.i++; const r=new TextDecoder().decode(this.d.slice(s,this.i)); this.i++; return r; }
}
function parseSchema(u8){
  const bb=new BB(u8); const n=bb.varUint(); const defs=[];
  for(let i=0;i<n;i++){ const name=bb.string(); const kind=bb.byte(); const fc=bb.varUint(); const fields=[];
    for(let j=0;j<fc;j++){ fields.push({name:bb.string(), type:bb.varInt(), isArray:!!bb.byte(), value:bb.varUint()}); }
    defs.push({name,kind,fields}); }
  return defs;
}
function makeReader(defs){
  function readType(bb,t){
    if(t>=0) return readDef(bb,defs[t]);
    switch(t){ case -1: return bb.bool(); case -2: return bb.byte(); case -3: return bb.varInt();
      case -4: return bb.varUint(); case -5: return bb.float(); case -6: return bb.string();
      case -7: return Number(bb.varInt64()); case -8: return Number(bb.varUint64()); }
    throw new Error('bad type '+t);
  }
  function readField(bb,f){ if(f.isArray){ const n=bb.varUint(); const a=[]; for(let i=0;i<n;i++) a.push(readType(bb,f.type)); return a; } return readType(bb,f.type); }
  function readDef(bb,def){
    if(def.kind===0){ const v=bb.varUint(); const f=def.fields.find(x=>x.value===v); return f?f.name:v; }
    if(def.kind===1){ const o={}; for(const f of def.fields) o[f.name]=readField(bb,f); return o; }
    const o={}; while(true){ const id=bb.varUint(); if(id===0) break; const f=def.fields.find(x=>x.value===id); if(!f) throw new Error('unknown field '+id+' in '+def.name); o[f.name]=readField(bb,f); } return o;
  }
  return {readDef, BB};
}
