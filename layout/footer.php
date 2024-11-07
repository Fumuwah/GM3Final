<script>
     
     var arrow = document.querySelector('#arrow');
            const width  = window.innerWidth || document.documentElement.clientWidth || 
            document.body.clientWidth;
            console.log(width)
            if(width < 768){
                var show = false;
            }else{
                var show = true;
            }

            arrow.addEventListener('click',function(){
                var sidebarContainer = document.querySelector('.sidebar-container');
            
                if(show){
                    sidebarContainer.style.marginLeft = '-250px';
                    arrow.style.transform = 'translate(-50%,-50%) rotate(180deg)';
                    show = false;
                }else{
                    sidebarContainer.style.marginLeft = '0px';
                    arrow.style.transform = 'translate(-50%,-50%) rotate(0deg)';
                    show = true;
                }
            
            });

        var accordionBtn = document.querySelectorAll('.accordion-btn');
        var accordions = document.querySelectorAll('.accordion');
        accordionBtn.forEach((d)=>{
            d.addEventListener('click',function(){
                var getAccordion = document.querySelector('#'+d.getAttribute('data-accordion'));
                if(this.innerHTML.trim() == "Resignation Form"){
                    window.open("assets/files/resignation.pdf", '_blank').focus();
                }
                accordionBtn.forEach((i)=>{
                    if(i != d){
                        i.classList.remove('btn-primary')
                    }else{
                        i.classList.add('btn-primary')
                    }
                  
                });
                accordions.forEach((d)=>{
                    if(d != getAccordion){
                        d.classList.remove('active');
                    }else{
                        d.classList.add('active');
                    }
                });
            });
        });

        
        var accountIcon = document.querySelector('#account-icon');
        var logoutDD = document.querySelector('#logout-dropdown');
        var logoutDropdown = false;
        accountIcon.addEventListener('click',function(){
            logoutDropdown = !logoutDropdown;
            if(logoutDropdown){
                logoutDD.style.display = "block";
            }else{
                logoutDD.style.display = "none";
            }
            
        });

        var notificationIcon = document.querySelector('#notification-icon');
        var notificationDD = document.querySelector('#notification-dropdown');
        var notificationDropdown = false;
        notificationIcon.addEventListener('click',function(){
            notificationDropdown = !notificationDropdown;
            if(notificationDropdown){
                notificationDD.style.display = "block";
            }else{
                notificationDD.style.display = "none";
            }
            
        })
        var DropdownLink =document.querySelectorAll('.dropdown-link');
        var Dropdown = document.querySelectorAll('.dropdown');
        var DropdownLinkIsClicked = false;
        DropdownLink.forEach((el)=>{
            el.addEventListener('click',function(event){
            
                let isDropdown = this.getAttribute('data-display');
                let getDropdown = this.parentNode.querySelectorAll('.dropdown')[0];
                // console.log(get)
                console.log(getDropdown)

                if (this !== event.target) return;

                

                if(isDropdown == "false"){
                    getDropdown.classList.add("active");
                    this.setAttribute('data-display',"true");
                }else{
                    getDropdown.classList.remove("active");
                    this.setAttribute('data-display',"false");
                }
            
            });
        });

      </script>
</body>
</html>