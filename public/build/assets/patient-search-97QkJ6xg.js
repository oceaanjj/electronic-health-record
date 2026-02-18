document.addEventListener("DOMContentLoaded",function(){const i=document.getElementById("patient-search"),a=document.querySelector(".w-full tbody"),l=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");if(!i||!a)return;function b(e){e.classList.remove("opacity-0","translate-y-2"),e.classList.add("opacity-100","translate-y-0")}function u(e){e.classList.remove("opacity-100","translate-y-0"),e.classList.add("opacity-0","translate-y-2")}let s="";function p(e){let n="";e.length>0?n=e.map(t=>`
                <tr class="${t.deleted_at?"bg-red-100 text-red-700":"bg-beige"} hover:bg-white hover:bg-opacity-50 transition-all duration-300 opacity-0 translate-y-2" data-id="${t.patient_id}">
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${t.patient_id}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30">
                        <a href="/patients/${t.patient_id}"
                            class="p-3 font-creato-black font-bold text-brown text-[13px] hover:underline hover:text-brown transition-colors duration-150">
                            ${t.name}
                        </a>
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${t.age}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${t.sex}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30 whitespace-nowrap text-center">
                        ${t.deleted_at?`<button type="button"
                                    class="inline-block bg-red-50 border border-red-600 text-red-600 cursor-pointer hover:bg-red-100 text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                    data-patient-id="${t.patient_id}" data-action="activate">RESTORE</button>`:`<a href="/patients/${t.patient_id}/edit"
                                    class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                    <button type="button"
                                        class="inline-block bg-red-600 cursor-pointer hover:bg-dark-red text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                        data-patient-id="${t.patient_id}" data-action="deactivate">SET INACTIVE</button>`}
                    </td>
                </tr>
            `).join(""):n=`
                <tr class="opacity-0 translate-y-2 transition-all duration-300">
                    <td colspan="5" class="p-4 text-center text-gray-500">No patients found.</td>
                </tr>
            `,n!==s&&(s=n,a.querySelectorAll("tr").forEach(t=>u(t)),setTimeout(()=>{a.innerHTML=n,a.querySelectorAll("tr").forEach(t=>{setTimeout(()=>b(t),50)})},200))}if(i&&a){let e;i.addEventListener("input",function(){const n=this.value.trim();clearTimeout(e),e=setTimeout(()=>{fetch(`/patients/live-search?input=${encodeURIComponent(n)}`).then(t=>t.json()).then(t=>{p(t)}).catch(t=>console.error("Error fetching patients:",t))},250)})}a.addEventListener("click",function(e){const n=e.target;if(!n.classList.contains("js-toggle-patient-status"))return;e.preventDefault();const t=n.dataset.patientId,d=n.dataset.action,f=`/patients/${t}/${d}`;fetch(f,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":l,"X-HTTP-Method-Override":d==="deactivate"?"DELETE":"POST"},body:JSON.stringify({})}).then(o=>o.json()).then(o=>{if(o.success&&o.patient){const r=n.closest("tr");if(!r)return;r.classList.add("transition-all","duration-300","ease-in-out"),o.patient.deleted_at?(r.classList.remove("bg-beige"),r.classList.add("bg-red-100","text-red-700")):(r.classList.remove("bg-red-100","text-red-700"),r.classList.add("bg-beige"));const c=r.querySelector("td:last-child");c&&(c.innerHTML=o.patient.deleted_at?`<button type="button"
                                class="inline-block bg-red-50 border border-red-600 text-red-600 cursor-pointer hover:bg-red-100 text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                data-patient-id="${o.patient.patient_id}" data-action="activate">RESTORE</button>`:`<a href="/patients/${o.patient.patient_id}/edit"
                                class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                <button type="button"
                                class="inline-block bg-red-600 cursor-pointer hover:bg-dark-red text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                data-patient-id="${o.patient.patient_id}" data-action="deactivate">SET INACTIVE</button>`)}}).catch(o=>console.error("Error toggling patient status:",o))})});
