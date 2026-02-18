let S,b;function E(e){let t=0;b=setInterval(()=>{t=(t+1)%4,e.textContent="Analyzing"+".".repeat(t)},400)}function L(){clearInterval(b)}function y(e){const t=e.dataset.fieldName,a=e.dataset.time;return a?document.querySelector(`[data-alert-for-time="${a}"]`):t?document.querySelector(`[data-alert-for="${t}"]`):null}function v(e){const t=e.querySelector(".cdss-btn");if(!t)return;const a=e.querySelectorAll(".cdss-input");let s=!1;for(let i=0;i<a.length;i++)if(a[i].value.trim()!==""){s=!0;break}s?(t.classList.remove("opacity-50","pointer-events-none","cursor-not-allowed"),t.tagName==="BUTTON"&&(t.disabled=!1)):(t.classList.add("opacity-50","pointer-events-none","cursor-not-allowed"),t.tagName==="BUTTON"&&(t.disabled=!0))}window.initializeCdssForForm=function(e){v(e);const t=e.dataset.analyzeUrl,a=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");e.querySelectorAll(".cdss-input").forEach(i=>{i.dataset.alertListenerAttached||(i.addEventListener("input",l=>{v(e);const d=l.target.value.trim(),c=y(l.target);if(c){if(d===""){h(c);return}c.querySelector(".glass-spinner")||A(c),clearTimeout(S),S=setTimeout(()=>{w(l.target.dataset.fieldName,d,l.target.dataset.time,c,t,a)},500)}}),i.dataset.alertListenerAttached="true")})};async function w(e,t,a,s,i,l,d=null){if(!s)return;let c={};if(a){let n=d||{};d||s.closest(".cdss-form")?.querySelectorAll(`.cdss-input[data-time="${a}"]`)?.forEach(r=>{const u=r.dataset.fieldName;n[u]=r.value.trim()}),c={time:a,vitals:n}}else c={fieldName:e,finding:t};try{const n=await fetch(i,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":l},body:JSON.stringify(c)});if(!n.ok)throw new Error(`Server error: ${n.status}`);const o=await n.json(),m=performance.now(),r=parseFloat(s.dataset.startTime||m),u=(m-r).toFixed(2);console.log(`[CDSS] Single response received in ${u} ms`),p(s,o,u)}catch(n){console.error("[CDSS] Single analysis failed:",n),p(s,{alert:"Error analyzing..."})}}window.triggerInitialCdssAnalysis=async function(e){const t=e.dataset.analyzeUrl,a=e.dataset.batchAnalyzeUrl,s=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");if(!a||!s){console.error('Missing "data-batch-analyze-url" or CSRF token.',e),t&&(console.warn(`[CDSS] data-batch-analyze-url not found!, 
 Falling back to old (single-analyze) analysis method.`),e.querySelectorAll(".cdss-input").forEach(o=>{const m=o.value.trim(),r=y(o);m!==""&&r?w(o.dataset.fieldName,m,o.dataset.time,r,t,s):r&&h(r)}));return}console.log(`[CDSS] Triggering BATCH analysis for form: ${e.id||"(unnamed)"}`);const i=e.querySelectorAll(".cdss-input"),l=new Map;i.forEach(n=>{const o=n.dataset.fieldName,m=n.value.trim(),r=n.dataset.time,u=y(n);if(!u)return;if(m===""){h(u);return}let f=r?`time-${r}`:`field-${o}`;l.has(f)||l.set(f,r?{time:r,alertCell:u,fields:{}}:{time:null,alertCell:u,fieldName:o,finding:m}),r&&(l.get(f).fields[o]=m)});const d=Array.from(l.values());if(d.length===0){console.log("[CDSS] No pre-filled inputs to analyze.");return}d.forEach(n=>{A(n.alertCell),n.alertCell.dataset.startTime=performance.now()});const c=d.map(n=>n.time?{time:n.time,vitals:n.fields}:{fieldName:n.fieldName,finding:n.finding});console.log(`[CDSS] Sending ${c.length} items for batch analysis...`);try{const n=await fetch(a,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":s},body:JSON.stringify({batch:c})});if(!n.ok)throw new Error(`Server error: ${n.status}`);const o=await n.json();if(!Array.isArray(o)||o.length!==d.length)throw new Error("Batch response mismatch or invalid format.");console.log(`[CDSS] Received ${o.length} batch results.`),o.forEach((m,r)=>{const f=d[r].alertCell,g=performance.now(),C=parseFloat(f.dataset.startTime||g),T=(g-C).toFixed(2);p(f,m,T)})}catch(n){console.error("[CDSS] Batch analysis failed:",n),d.forEach(o=>{p(o.alertCell,{alert:"Batch Error"})})}};function p(e,t){if(!e)return;L();let a=t.alert&&!t.alert.toLowerCase().includes("no findings");a?(e.innerHTML=`
            <div class="alert-wrapper">
                <div class="alert-icon-btn is-active fade-in">
                    <span class="material-symbols-outlined">add_alert</span>
                </div>
                <div class="alert-bubble show-pop hidden md:block">
                    <span class="font-bold" style="color: #f59e0b;">Alert available!</span>
                </div>
            </div>
        `,e.querySelector(".alert-icon-btn").addEventListener("click",()=>F(t))):e.innerHTML=`
            <div class="alert-wrapper">
                <div class="alert-icon-btn">
                    <span class="material-symbols-outlined">notifications</span>
                </div>
                <div class="alert-bubble show-pop hidden md:block">
                    <span class="text-gray-400">No alerts.</span>
                </div>
            </div>
        `,setTimeout(()=>{const s=e.querySelector(".alert-bubble"),i=e.querySelector(".alert-wrapper");s&&(s.style.filter="blur(10px)",s.style.opacity="0",s.style.transform="translateY(-10px)",setTimeout(()=>{s.remove(),!a&&i&&i.classList.add("is-dimmed")},500))},3e3)}function h(e){e&&(e.innerHTML=`
        <div class="alert-wrapper is-dimmed">
            <div class="alert-icon-btn">
                <span class="material-symbols-outlined">notifications</span>
            </div>
        </div>
    `)}function A(e){e&&(e.innerHTML=`
        <div class="alert-wrapper">
            <div class="alert-icon-btn" style="background: rgba(59, 130, 246, 0.1);">
                <div class="glass-spinner"></div>
            </div>
            <div class="alert-bubble show-pop hidden md:block">
                <span class="text-blue-500 font-medium" id="loading-text">Analyzing</span>
            </div>
        </div>
    `,E(document.getElementById("loading-text")))}function F(e){if(document.querySelector(".alert-modal-overlay"))return;const t=document.createElement("div");t.className="alert-modal-overlay";const a=e.alert.includes(";")?`<ul class="list-disc list-inside text-left">${e.alert.split("; ").map(l=>`<li>${l.trim()}</li>`).join("")}</ul>`:`<p>${e.alert}</p>`,s=document.createElement("div");s.className="alert-modal fade-in",s.innerHTML=`
      <button class="close-btn">&times;</button>
      <h2>Alert Details</h2>
      ${a}
      ${e.recommendation?`<h3>Recommendation:</h3><p>${e.recommendation}</p>`:""}
    `,t.appendChild(s),document.body.appendChild(t);const i=()=>{t.remove()};t.addEventListener("click",l=>{l.target===t&&i()}),s.querySelector(".close-btn").addEventListener("click",i)}(function(){const e=document.createElement("style");e.textContent=`
      .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
      @keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
    `,document.head.appendChild(e)})();function N(){console.log("[CDSS] Initializing all forms..."),document.querySelectorAll(".cdss-form").forEach(t=>{window.initializeCdssForForm(t),window.triggerInitialCdssAnalysis(t)})}window.cdssFormReloadListenerAttached||(window.cdssFormReloadListenerAttached=!0,document.addEventListener("cdss:form-reloaded",e=>{const a=e.detail.formContainer.querySelector(".cdss-form");a&&(console.log("[CDSS] Form reloaded — reinitializing CDSS"),window.initializeCdssForForm(a),window.triggerInitialCdssAnalysis(a))}));window.cdssDomLoadListenerAttached||(window.cdssDomLoadListenerAttached=!0,document.addEventListener("DOMContentLoaded",()=>{window.cdssFormReloaded!==!0&&(console.log("[CDSS] DOM fully loaded — initializing all CDSS forms"),N())}));
