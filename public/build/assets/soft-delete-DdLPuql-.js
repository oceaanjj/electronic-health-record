document.addEventListener("DOMContentLoaded",function(){const i=document.querySelector(".ehr-table");i&&i.addEventListener("click",function(n){if(n.target.classList.contains("btn-delete")||n.target.classList.contains("btn-recover")){n.preventDefault();const o=n.target,r=o.closest("form"),t=o.closest("tr"),a=o.classList.contains("btn-delete"),d=r.action,s=r.querySelector('input[name="_token"]').value;fetch(d,{method:"POST",headers:{"X-CSRF-TOKEN":s,"Content-Type":"application/json",Accept:"application/json"},body:JSON.stringify({_method:a?"DELETE":"POST"})}).then(e=>e.json()).then(e=>{if(e.success){const l=t.querySelector("td:last-child"),c=t.dataset.id;a?(t.style.backgroundColor="#ffdddd",t.style.color="red",l.innerHTML=`
                            <form action="/patients/${c}/recover" method="POST" style="display:inline;">
                                <input type="hidden" name="_token" value="${s}">
                                <button type="submit" class="btn-recover">Recover</button>
                            </form>
                        `):(t.style.backgroundColor="",t.style.color="",l.innerHTML=`
                            <a href="/patients/${c}/edit" class="btn-edit">Edit</a>
                            <form action="/patients/${c}" method="POST" style="display:inline;">
                                <input type="hidden" name="_token" value="${s}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        `)}}).catch(e=>console.error("Error:",e))}})});
