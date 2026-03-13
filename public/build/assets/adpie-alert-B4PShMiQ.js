let f;function h(e){const t=e.dataset.fieldName;return{recommendation:document.getElementById(`recommendation-${t}`),noRecommendation:document.getElementById(`no-recommendation-${t}`)}}function g(e){if(!e)return"";if(e.includes("<ul>")||e.includes("<ol>")||e.includes("<li>"))return e;let t=[];const i=e.split(`
`).map(n=>n.trim()).filter(n=>n.length>0);return i.length>1?t=i.map(n=>n.replace(/^[\d\-\*\•]+[\.\):\s]*/,"").trim()).filter(n=>n.length>0):t=e.split(/\.\s+/).map(n=>n.trim()).filter(n=>n.length>0),t.length>1?`<ul style="margin: 0; padding-left: 1.5rem; list-style-type: disc;">${t.map(l=>`<li style="margin-bottom: 0.5rem; line-height: 1.6;">${l.match(/[.!?]$/)?l:l+"."}</li>`).join("")}</ul>`:`<p style="margin: 0; line-height: 1.6;">${e.match(/[.!?]$/)?e:e+"."}</p>`}window.initializeAdpieCdssForForm=function(e){const t=e.dataset.analyzeUrl,i=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content"),r=e.dataset.component,n=e.dataset.patientId;if(!t||!i||!r||!n){console.error("[ADPIE] Missing form data: analyze-url, component, patient-id, or CSRF token.",e);return}console.log(`[ADPIE] Initializing typing listeners for: ${r}`),e.querySelectorAll(".cdss-input").forEach(s=>{if(s.dataset.alertListenerAttached)return;const o=d=>{clearTimeout(f);const m=d.target.dataset.fieldName,p=m.charAt(0).toUpperCase()+m.slice(1),a=d.target.value.trim(),c=h(d.target);if(a===""){b(c);return}f=setTimeout(()=>{m&&a!==""&&(console.log(`[ADPIE] Input → Field: ${p} | Value: ${a}`),w(c),v(m,a,n,r,c,t,i))},800)};s.addEventListener("input",o),s.dataset.alertListenerAttached="true"})};async function v(e,t,i,r,n,l,s){if(n.recommendation){console.log(`[ADPIE] Sending single analysis for: ${e}`);try{const o=await fetch(l,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":s},body:JSON.stringify({fieldName:e,finding:t,patient_id:i,component:r})});if(!o.ok)throw new Error(`Server error: ${o.status}`);const d=await o.json();console.log(`[ADPIE] Single response received for: ${e}`,d),u(n,d)}catch(o){console.error("[ADPIE] Single analysis failed:",o),u(n,{message:"Error analyzing field. Please try again.",level:"CRITICAL"})}}}function u(e,t){if(!e.recommendation)return;console.log(`[ADPIE] Displaying banner → Level: ${t.level} | Message: ${t.message}`);let i="alert-green",r="info",n="Clinical Decision Support";if(t.level==="CRITICAL"?(i="alert-red",r="error",n="Critical Alert"):t.level==="WARNING"&&(i="alert-orange",r="warning",n="Warning"),!t.message||t.message.toLowerCase().includes("no findings")||t.message.toLowerCase().includes("no recommendations")||t.message.toLowerCase().includes("type more")||t.message.trim()===""){b(e);return}const l=g(t.message),s=x(l).substring(0,60)+"...";e.noRecommendation&&e.noRecommendation.classList.add("hidden");const o=e.recommendation;o.className=`recommendation-banner ${i}`,o.classList.remove("hidden"),o.id.replace("recommendation-",""),o.innerHTML=`
        <div class="banner-content">
            <div class="banner-icon">
                <span class="material-symbols-outlined">${r}</span>
            </div>
            <div class="banner-text">
                <div class="banner-title">${n}</div>
                <div class="banner-subtitle">
                    ${s}
                </div>
            </div>
        </div>
        <div class="banner-action">
            <span>View Details</span>
            <span class="material-symbols-outlined">arrow_forward</span>
        </div>
    `,o.dataset.fullMessage=l,o.dataset.levelText=n,o.dataset.levelIcon=r,o.dataset.levelIconColor=y(t.level),o.onclick=function(){window.openRecommendationModal(this)}}function y(e){return e==="CRITICAL"?"#ef4444":e==="WARNING"?"#f59e0b":"#059669"}function b(e){e.recommendation&&e.recommendation.classList.add("hidden"),e.noRecommendation&&e.noRecommendation.classList.remove("hidden")}function w(e){const t=e.recommendation;t&&(e.noRecommendation&&e.noRecommendation.classList.add("hidden"),t.className="recommendation-banner alert-green",t.classList.remove("hidden"),t.innerHTML=`
        <div class="banner-content">
            <div class="banner-icon">
                <div class="banner-loading-spinner"></div>
            </div>
            <div class="banner-text">
                <div class="banner-title">Analyzing...</div>
                <div class="banner-subtitle">Please wait while we review your input</div>
            </div>
        </div>
        <div class="banner-action" style="opacity: 0.5; pointer-events: none;">
            <div class="banner-loading-spinner" style="width: 16px; height: 16px;"></div>
        </div>
    `,t.onclick=null)}function x(e){const t=document.createElement("div");return t.innerHTML=e,t.textContent||t.innerText||""}window.openRecommendationModal=function(e){let t=e.dataset.fullMessage,i=e.dataset.levelText,r=e.dataset.levelIcon,n=e.dataset.levelIconColor;if(t||(t=e.querySelector(".banner-subtitle")?.dataset.fullMessage),i||(i=e.querySelector(".banner-title")?.textContent||"Recommendation"),(!r||!n)&&(i.toLowerCase().includes("critical")?(r="error",n="#ef4444"):i.toLowerCase().includes("warning")?(r="warning",n="#f59e0b"):(r="info",n="#10b981")),!t){console.error("No message available");return}const l=g(t),s=document.createElement("div");s.className="alert-modal-overlay fade-in";const o=document.createElement("div");o.className="alert-modal fade-in",o.innerHTML=`
        <button class="close-btn" aria-label="Close">×</button>

        <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: ${n}15; display: flex; align-items: center; justify-content: center;">
                <span class="material-symbols-outlined" style="color: ${n}; font-size: 1.75rem;">${r}</span>
            </div>
            <div>
                <h2 style="margin: 0; font-size: 1.5rem;">${i}</h2>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #6b7280;">Recommendation</p>
            </div>
        </div>

        <div class="modal-content-scroll" style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem; margin-top: 1.5rem;">
            ${l}
        </div>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.75rem; color: #6b7280;">
                💡 Press <kbd style="padding: 2px 6px; background: #f3f4f6; border-radius: 4px; font-family: monospace;">ESC</kbd> to close
            </span>
            <button class="close-action-btn" style="padding: 0.625rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                Got it
            </button>
        </div>
    `,s.appendChild(o),document.body.appendChild(s);const d=()=>{s.remove(),document.removeEventListener("keydown",m)};s.addEventListener("click",c=>{c.target===s&&d()}),o.querySelector(".close-btn").addEventListener("click",d),o.querySelector(".close-action-btn").addEventListener("click",d);const m=c=>{c.key==="Escape"&&d()};document.addEventListener("keydown",m);const p=o.querySelector(".close-btn");p&&p.focus();const a=o.querySelector(".close-action-btn");a.addEventListener("mouseenter",()=>{a.style.transform="translateY(-2px)",a.style.boxShadow="0 4px 16px rgba(16, 185, 129, 0.4)",a.style.filter="brightness(1.1)"}),a.addEventListener("mouseleave",()=>{a.style.transform="translateY(0)",a.style.boxShadow="0 2px 8px rgba(16, 185, 129, 0.25)",a.style.filter="brightness(1)"}),a.addEventListener("mousedown",()=>{a.style.transform="translateY(0) scale(0.95)"}),a.addEventListener("mouseup",()=>{a.style.transform="translateY(-2px) scale(1)"})};(function(){if(document.getElementById("adpie-banner-styles"))return;const e=document.createElement("style");e.id="adpie-banner-styles",e.textContent=`
        .fade-in { 
            animation: fadeIn 0.3s ease-in-out forwards; 
        }
        
        @keyframes fadeIn { 
            from { opacity: 0; transform: scale(0.98); } 
            to { opacity: 1; transform: scale(1); } 
        }
        
        .alert-modal-overlay {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000; backdrop-filter: blur(5px);
        }
        
        .alert-modal {
            background: white; 
            padding: 2rem; 
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 600px; 
            width: 90%; 
            max-height: 80vh;
            position: relative; 
            color: #333;
            overflow: hidden;
        }
        
        .alert-modal h2 {
            margin: 0;
            font-size: 1.5rem; 
            font-weight: 600; 
            color: #222;
        }
        
        .alert-modal h3 {
            font-size: 1.1rem; 
            font-weight: 600;
            margin-top: 1.5rem; 
            margin-bottom: 0.75rem; 
            color: #444;
        }
        
        .alert-modal p { 
            font-size: 1rem; 
            line-height: 1.6; 
            margin-bottom: 0.75rem;
        }

        .alert-modal ul, .alert-modal ol {
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }

        .alert-modal li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .alert-modal .close-btn {
            position: absolute; 
            top: 15px; 
            right: 20px;
            font-size: 24px;
            font-weight: 300; 
            color: #9ca3af;
            background: transparent; 
            border: none !important;
            outline: none !important;
            cursor: pointer; 
            line-height: 1;
            transition: all 0.2s ease;
            z-index: 10;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 0;
            box-shadow: none !important;
        }
        
        .alert-modal .close-btn:hover { 
            color: #1f2937; 
            background: #f3f4f6 !important;
            transform: scale(1.1);
            border: none !important;
            outline: none !important;
        }

        .alert-modal .close-btn:active {
            transform: scale(0.95);
            background: #e5e7eb !important;
            border: none !important;
            outline: none !important;
        }

        .alert-modal .close-btn:focus {
            outline: none !important;
            border: none !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        .modal-content-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .modal-content-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .modal-content-scroll::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .modal-content-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .modal-content-scroll::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        kbd {
            padding: 2px 6px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.875em;
        }
    `,document.head.appendChild(e)})();function C(){console.log("[ADPIE] Initializing all forms with banner system"),document.querySelectorAll(".cdss-form").forEach(t=>{t.dataset.component&&window.initializeAdpieCdssForForm(t)})}window.adpieCdssFormReloadListenerAttached||(window.adpieCdssFormReloadListenerAttached=!0,document.addEventListener("cdss:form-reloaded",e=>{const i=e.detail.formContainer.querySelector(".cdss-form");i&&i.dataset.component&&(console.log("[ADPIE] Form reloaded — reinitializing banner listeners"),window.initializeAdpieCdssForForm(i))}));window.adpieCdssStepChangeListenerAttached||(window.adpieCdssStepChangeListenerAttached=!0,document.addEventListener("cdss:step-changed",e=>{const t=e.detail.form;t&&t.dataset.component&&(console.log(`[ADPIE] Step changed to ${e.detail.step} — reinitializing banner listeners`),window.initializeAdpieCdssForForm(t))}));window.adpieCdssDomLoadListenerAttached||(window.adpieCdssDomLoadListenerAttached=!0,document.addEventListener("DOMContentLoaded",()=>{window.cdssFormReloaded!==!0&&(console.log("[ADPIE] DOM loaded — initializing banner system"),C())}));
