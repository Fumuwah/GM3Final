<script>
    $(document).ready(function() {
        $(window).resize(function() {
            let width = $(window).width();
            if (width < 992) {
                $('#sidebar').attr('class', '');
                $('#sidebar-notif, #sidebar-logout').removeAttr('hidden');
            } else {
                $('#sidebar').attr('class', 'expand');
                $('#sidebar-notif, #sidebar-logout').attr('hidden', 'hidden');
            }
        });
        $(window).resize();

        $('#collapse1').on('click', function() {
            $('#collapsetest1').slideToggle()
        })
        $('#collapse2').on('click', function() {
            $('#collapsetest2').slideToggle()
        })

        // Toggle Logout Dropdown
        let logoutDropdown = false;
        $('#account-icon').on('click', function() {
            logoutDropdown = !logoutDropdown;
            if (logoutDropdown) {
                $('#logout-dropdown').show();
            } else {
                $('#logout-dropdown').hide();
            }
        });

        // Toggle Notification Dropdown
        let notificationDropdown = false;
        $('#notification-icon').on('click', function() {
            notificationDropdown = !notificationDropdown;
            if (notificationDropdown) {
                $('#notification-dropdown').show();
            } else {
                $('#notification-dropdown').hide();
            }
        });

        let notificationSidebar = false;
        $('#notification-toggler').on('click', function() {
            notificationSidebar = !notificationSidebar;
            if (notificationSidebar) {
                $('#notification-sidebar').show();
            } else {
                $('#notification-sidebar').hide();
            }
        });

        // Toggle Custom Dropdowns
        $('.dropdown-link').on('click', function(event) {
            let isDropdown = $(this).attr('data-display');
            let getDropdown = $(this).siblings('.dropdown').first();

            if (this !== event.target) return;

            if (isDropdown === "false") {
                getDropdown.addClass("active");
                $(this).attr('data-display', "true");
            } else {
                getDropdown.removeClass("active");
                $(this).attr('data-display', "false");
            }
        });

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

    // var resigBtn = document.getElementById('rform');
    // resigBtn.addEventListener('click', function() {
    //     window.open("assets/files/resignation.pdf", '_blank').focus();
    // });
</script>
</body>

</html>