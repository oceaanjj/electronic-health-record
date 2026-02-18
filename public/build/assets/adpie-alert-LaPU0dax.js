let p;function b(e){return{recommendation:document.getElementById("recommendation-banner"),noRecommendation:document.getElementById("no-recommendation-banner")}}function h(e){if(!e)return"";if(e.includes("<ul>")||e.includes("<ol>")||e.includes("<li>"))return e;let n=[];const i=e.split(`
`).map(t=>t.trim()).filter(t=>t.length>0);return i.length>1?n=i.map(t=>t.replace(/^[\d\-\*\•]+[\.\):\s]*/,"").trim()).filter(t=>t.length>0):n=e.split(/\.\s+/).map(t=>t.trim()).filter(t=>t.length>0),n.length>1?`<ul style="margin: 0; padding-left: 1.5rem; list-style-type: disc;">${n.map(a=>`<li style="margin-bottom: 0.5rem; line-height: 1.6;">${a.match(/[.!?]$/)?a:a+"."}</li>`).join("")}</ul>`:`<p style="margin: 0; line-height: 1.6;">${e.match(/[.!?]$/)?e:e+"."}</p>`}window.initializeAdpieCdssForForm=function(e){const n=e.dataset.analyzeUrl,i=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content"),r=e.dataset.component,t=e.dataset.patientId;if(!n||!i||!r||!t){console.error("[ADPIE] Missing form data: analyze-url, component, patient-id, or CSRF token.",e);return}console.log(`[ADPIE] Initializing typing listeners for: ${r}`),e.querySelectorAll(".cdss-input").forEach(s=>{if(s.dataset.alertListenerAttached)return;const o=l=>{clearTimeout(p);const d=l.target.dataset.fieldName,g=d.charAt(0).toUpperCase()+d.slice(1),c=l.target.value.trim(),m=b(l.target);if(c===""){u(m);return}p=setTimeout(()=>{d&&c!==""&&(console.log(`[ADPIE] Input → Field: ${g} | Value: ${c}`),y(m),v(d,c,t,r,m,n,i))},800)};s.addEventListener("input",o),s.dataset.alertListenerAttached="true"})};async function v(e,n,i,r,t,a,s){if(t.recommendation){console.log(`[ADPIE] Sending single analysis for: ${e}`);try{const o=await fetch(a,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":s},body:JSON.stringify({fieldName:e,finding:n,patient_id:i,component:r})});if(!o.ok)throw new Error(`Server error: ${o.status}`);const l=await o.json();console.log(`[ADPIE] Single response received for: ${e}`,l),f(t,l)}catch(o){console.error("[ADPIE] Single analysis failed:",o),f(t,{message:"Error analyzing field. Please try again.",level:"CRITICAL"})}}}function f(e,n){if(!e.recommendation)return;console.log(`[ADPIE] Displaying banner → Level: ${n.level} | Message: ${n.message}`);let i="alert-green",r="info",t="Clinical Decision Support";if(n.level==="CRITICAL"?(i="alert-red",r="error",t="Critical Alert"):n.level==="WARNING"&&(i="alert-orange",r="warning",t="Warning"),!n.message||n.message.toLowerCase().includes("no findings")||n.message.toLowerCase().includes("no recommendations")||n.message.toLowerCase().includes("type more")||n.message.trim()===""){u(e);return}const a=h(n.message),s=I(a).substring(0,60)+"...";e.noRecommendation&&e.noRecommendation.classList.add("hidden");const o=e.recommendation;o.className=`recommendation-banner ${i}`,o.classList.remove("hidden"),o.innerHTML=`
        <div class="banner-content">
            <div class="banner-icon">
                <span class="material-symbols-outlined">${r}</span>
            </div>
            <div class="banner-text">
                <div class="banner-title">${t}</div>
                <div class="banner-subtitle">
                    ${s}
                </div>
            </div>
        </div>
        <div class="banner-action">
            <span>View Details</span>
            <span class="material-symbols-outlined">arrow_forward</span>
        </div>
    `,o.dataset.fullMessage=a,o.dataset.levelText=t,o.dataset.levelIcon=r,o.dataset.levelIconColor=w(n.level),o.onclick=function(){window.openRecommendationModal(this)}}function w(e){return e==="CRITICAL"?"#ef4444":e==="WARNING"?"#f59e0b":"#059669"}function u(e){console.log('[ADPIE] Showing "No Recommendations Yet" banner'),e.recommendation&&e.recommendation.classList.add("hidden"),e.noRecommendation&&e.noRecommendation.classList.remove("hidden")}function y(e){const n=e.recommendation;n&&(console.log("[ADPIE] Analyzing... showing banner loader"),e.noRecommendation&&e.noRecommendation.classList.add("hidden"),n.className="recommendation-banner alert-green",n.classList.remove("hidden"),n.innerHTML=`
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
    `,n.onclick=null)}function I(e){const n=document.createElement("div");return n.innerHTML=e,n.textContent||n.innerText||""}(function(){if(document.getElementById("adpie-banner-styles"))return;const e=document.createElement("style");e.id="adpie-banner-styles",e.textContent=`
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
    `,document.head.appendChild(e)})();function x(){console.log("[ADPIE] Initializing all forms with banner system"),document.querySelectorAll(".cdss-form").forEach(n=>{n.dataset.component&&window.initializeAdpieCdssForForm(n)})}window.adpieCdssFormReloadListenerAttached||(window.adpieCdssFormReloadListenerAttached=!0,document.addEventListener("cdss:form-reloaded",e=>{const i=e.detail.formContainer.querySelector(".cdss-form");i&&i.dataset.component&&(console.log("[ADPIE] Form reloaded — reinitializing banner listeners"),window.initializeAdpieCdssForForm(i))}));window.adpieCdssDomLoadListenerAttached||(window.adpieCdssDomLoadListenerAttached=!0,document.addEventListener("DOMContentLoaded",()=>{window.cdssFormReloaded!==!0&&(console.log("[ADPIE] DOM loaded — initializing banner system"),x())}));
