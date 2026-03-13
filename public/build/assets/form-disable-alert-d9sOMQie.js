document.addEventListener("DOMContentLoaded",function(){let i;const t=document.createElement("div");t.id="patient-selection-alert",t.style.cssText=`
        position: fixed;
        bottom: 20px;
        right: 20px; /* Moved to Right */
        background-color: #dc3545;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        font-family: sans-serif;
        font-size: 16px;
        text-align: center;
        pointer-events: none;
        
        opacity: 0;
        transform: translateY(20px); 
        transition: opacity 0.2s ease, transform 0.2s ease;
        visibility: hidden; /* Prevents clicking when invisible */
    `,t.textContent="Please select a patient first!",document.body.appendChild(t);function n(){clearTimeout(i),t.style.visibility="visible",requestAnimationFrame(()=>{t.style.opacity="1",t.style.transform="translateY(0)"}),i=setTimeout(()=>{t.style.opacity="0",t.style.transform="translateY(20px)",setTimeout(()=>{t.style.opacity==="0"&&(t.style.visibility="hidden")},200)},2e3)}function a(){const e=document.getElementById("patient_id_hidden");return e&&e.value.trim()!==""}document.body.addEventListener("click",function(e){(e.target.classList.contains("trigger-patient-alert")||e.target.closest(".trigger-patient-alert"))&&(e.preventDefault(),e.stopPropagation(),a()||n())})});
