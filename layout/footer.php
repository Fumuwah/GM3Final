<script>
    $(document).ready(function() {
        $(window).resize(function() {
            let width = $(window).width();
            if (width < 992) {
                $('#sidebar').attr('class', '');
            } else {
                $('#sidebar').attr('class', 'expand');
            }
        });
        $(window).resize();

        $('#collapse1').on('click', function() {
            $('#collapsetest1').slideToggle()
        })
        $('#collapse2').on('click', function() {
            $('#collapsetest2').slideToggle()
        })

    });

    let arrow = document.querySelector("#arrow");
    arrow.addEventListener("click", function() {
        document.querySelector("#sidebar").classList.toggle("expand");
        arrow.classList.toggle("rotate");
    });

    var accordionBtn = document.querySelectorAll('.accordion-btn');
    var accordions = document.querySelectorAll('.accordion');
    accordionBtn.forEach((d) => {
        d.addEventListener('click', function() {
            var getAccordion = document.querySelector('#' + d.getAttribute('data-accordion'));
            accordions.forEach((d) => {
                if (d != getAccordion) {
                    d.classList.remove('active');
                } else {
                    d.classList.add('active');
                }
            });
        });
    });


    var accountIcon = document.querySelector('#account-icon');
    var logoutDD = document.querySelector('#logout-dropdown');
    var logoutDropdown = false;
    accountIcon.addEventListener('click', function() {
        logoutDropdown = !logoutDropdown;
        if (logoutDropdown) {
            logoutDD.style.display = "block";
        } else {
            logoutDD.style.display = "none";
        }

    });

    var notificationIcon = document.querySelector('#notification-icon');
    var notificationDD = document.querySelector('#notification-dropdown');
    var notificationDropdown = false;
    notificationIcon.addEventListener('click', function() {
        notificationDropdown = !notificationDropdown;
        if (notificationDropdown) {
            notificationDD.style.display = "block";
        } else {
            notificationDD.style.display = "none";
        }

    })
    var DropdownLink = document.querySelectorAll('.dropdown-link');
    var Dropdown = document.querySelectorAll('.dropdown');
    var DropdownLinkIsClicked = false;
    DropdownLink.forEach((el) => {
        el.addEventListener('click', function(event) {

            let isDropdown = this.getAttribute('data-display');
            let getDropdown = this.parentNode.querySelectorAll('.dropdown')[0];
            // console.log(get)
            console.log(getDropdown)

            if (this !== event.target) return;



            if (isDropdown == "false") {
                getDropdown.classList.add("active");
                this.setAttribute('data-display', "true");
            } else {
                getDropdown.classList.remove("active");
                this.setAttribute('data-display', "false");
            }

        });
    });
</script>
</body>

</html>