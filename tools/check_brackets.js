const fs = require('fs');
const s = fs.readFileSync('weather1/app/app.js','utf8');
let stack = [];
let inS=false,inD=false,inB=false;
for(let i=0;i<s.length;i++){
  const ch = s[i];
  if(ch==='\\') { i++; continue; }
  if(inS){ if(ch === "'") inS=false; continue; }
  if(inD){ if(ch === '"') inD=false; continue; }
  if(inB){ if(ch === '`') inB=false; continue; }
  if(ch === "'") { inS=true; continue; }
  if(ch === '"') { inD=true; continue; }
  if(ch === '`') { inB=true; continue; }
  if('([{'.includes(ch)) stack.push({ch,i});
  else if(')]}'.includes(ch)){
    if(stack.length===0){ console.error('Unmatched closing',ch,'at',i); process.exit(2); }
    const last = stack.pop();
    const pairs = { '(':')','[':']','{':'}' };
    if(pairs[last.ch] !== ch){ console.error('Mismatched', last.ch,'at',last.i,'with',ch,'at',i); process.exit(3); }
  }
}
if(stack.length>0){ console.error('Unclosed brackets remain:', stack.map(x=>x.ch+'@'+x.i).join(', ')); process.exit(4); }
console.log('Brackets OK');
