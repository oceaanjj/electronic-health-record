(function(){let p;function y(){const n=document.getElementById("io-form");if(!n){console.warn("Intake/Output CDSS: #io-form not found.");return}const o=n.dataset.analyzeUrl,r=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");if(!o||!r){console.error('Intake/Output CDSS: Form missing "data-analyze-url" or CSRF token not found.');return}const c=n.querySelectorAll(".cdss-input"),d=document.querySelector('[data-alert-for="io_alert"]'),l=d||null;if(!l){console.warn('Intake/Output CDSS: Alert box div with data-alert-for="io_alert" not found.');return}function s(e){e.classList.remove("alert-loading","alert-red","alert-orange","alert-green"),e.classList.add("has-no-alert","alert-green"),e.innerHTML=`
                <span class="opacity-70 text-white font-semibold text-center">NO ALERTS</span>
            `,e.onclick=null}function v(e){e.classList.remove("has-no-alert","alert-red","alert-orange","alert-green"),e.classList.add("alert-loading"),e.innerHTML=`
                <div class="alert-message" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <div class="loading-spinner"></div>
                    <span>Analyzing...</span>
                </div>
            `,e.onclick=null}function u(e,a){e.classList.remove("alert-loading","has-no-alert","alert-red","alert-orange","alert-green");let t="alert-green";a.severity==="CRITICAL"?t="alert-red":a.severity==="WARNING"?t="alert-orange":a.severity==="INFO"&&(t="alert-green"),e.classList.add(t);let i;a.alert?.toLowerCase().includes("no findings")?(e.classList.add("has-no-alert"),i=`
                    <span class="opacity-70 text-white text-center uppercase font-semibold">
                        NO FINDINGS
                    </span>
                `,e.onclick=null):(i=`<span>${a.alert}</span>`,e.onclick=()=>I(a)),e.innerHTML=`
                <div class="alert-message">${i}</div>
            `}const L=()=>{const e=n.querySelector('[name="oral_intake"]'),a=n.querySelector('[name="iv_fluids_volume"]'),t=n.querySelector('[name="urine_output"]');return{oral_intake:e?e.value.trim():"",iv_fluids_volume:a?a.value.trim():"",urine_output:t?t.value.trim():""}},f=async()=>{const e=L();if(Object.values(e).every(t=>t===""))typeof s=="function"?s(l):console.error("showDefaultNoAlertsLocal function not available");else{typeof v=="function"?v(l):console.error("showAlertLoadingLocal function not available");try{const t=await fetch(o,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":r},body:JSON.stringify(e)});if(!t.ok)throw new Error(`Server error: ${t.status}`);const i=await t.json();setTimeout(()=>{typeof u=="function"?u(l,i):console.error("displayAlertLocal function not available")},150)}catch(t){console.error("Intake/Output CDSS analysis failed:",t),typeof u=="function"&&u(l,{alert:"Error analyzing...",severity:"CRITICAL"})}}};c.forEach(e=>{e.addEventListener("input",()=>{clearTimeout(p),p=setTimeout(f,300)})});const h=L(),S=Object.values(h).every(e=>e===""),C=n.querySelector('input[name="patient_id"]'),m=C&&C.value;!S&&m?f():S&&m?typeof s=="function"&&s(l):m||typeof s=="function"&&s(l),document.addEventListener("io:data-loaded",e=>{console.log("io:data-loaded event received, re-triggering CDSS analysis."),e.detail.ioData?f():s(l)})}document.addEventListener("DOMContentLoaded",y),document.addEventListener("cdss:form-reloaded",n=>{n.detail.formContainer.querySelector("#io-form")&&y()});function I(n){const o=document.createElement("div");o.className="alert-modal-overlay";const r=document.createElement("div");r.className="alert-modal fade-in",r.innerHTML=`
            <button class="close-btn">&times;</button>
            <h2>Alert Details</h2>
            <p>${n.alert}</p>
        `,o.appendChild(r),document.body.appendChild(o);const c=()=>o.remove();o.addEventListener("click",d=>{d.target===o&&c()}),r.querySelector(".close-btn").addEventListener("click",c)}const g=document.createElement("style");g.textContent=`
        .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    `,document.head.appendChild(g)})();
