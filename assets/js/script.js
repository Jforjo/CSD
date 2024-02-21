(() => {
    "use strict";

    window.onload = (e) => {
        LinkHandler('admin/dashboard', 'Dashboard', {
            'admin': 'admin/dashboard',
            'dashboard': 'admin/dashboard'
        });
    };

    const navList = document.querySelectorAll('nav ul li');
    navList.forEach((navItem, index) => {
        if (navItem.classList.contains('listening')) return;
        navItem.addEventListener('click', (e) => {
            // Prevent the a tag from redirecting
            e.preventDefault();
            // If it's already active
            if (navItem.classList.contains('active')) return;
            // Remove active class from the previous active link
            navList.forEach(navItem => {
                navItem.classList.remove('active');
                navItem.querySelector('svg')?.classList.remove('nc-int-icon-state-b');
            });
            // make this link active
            navItem.querySelector('svg')?.classList.add('nc-int-icon-state-b');
            navItem.classList.add('active');
            document.querySelector('nav .indicator')?.style.setProperty('--pos', index);
            // Load the page
            let link = navItem.querySelector('a')?.dataset?.link;
            // Turns "admin/student-management.html" into "Student Management"
            let title = link?.split('/').pop().split('.')[0].split('-').map(str => str.charAt(0).toUpperCase() + str.substring(1)).join(' ');
            LinkHandler(link, title);
            
        });
    });
    
    function LinkHandler(link, title = null, breadcrumbs = null) {
        if (link == null) return;
        fetch(`/${link}`, {
            // body: formData
        }).then(res => {
            if (res.status >= 200 && res.status < 300) {
                return res.text();
            }
            throw new Error(res.statusText);
        }).then(data => {
            document.body.querySelector('main').innerHTML = data;
            title != null && (document.getElementById('page-title').innerText = title);
            // breadcrumbs != null && Breadcrumbs(breadcrumbs);
            // history.pushState({}, "", '/' + link.split('.')[0]);
            LoadPageEvents();
        }).catch(error => {
            if (error === null || error === '') error = "An Unknown Error Occurred";
            console.error("error", error);
        });
    }
    /*
    function Breadcrumbs(crumbs) {
        if (crumbs == null) return;
        if (typeof crumbs !== 'object') return;
        const breadcrumbsElement = document.getElementById('breadcrumbs');
        breadcrumbsElement.innerHTML = '';
        Object.keys(crumbs).forEach((key, index, arr) => {
            // The "data-link" existing decides whether it is underlined or not by the CSS.
            if (crumbs[key] == '')
                breadcrumbsElement.innerHTML += `<a>${key}</a>`;
            else
                breadcrumbsElement.innerHTML += `<a data-link="${crumbs[key]}">${key}</a>`;
            if (index < arr.length - 1)
                breadcrumbsElement.innerHTML += '<i>/</i>';
        });
       
    }
    function GetCrumbsFromLink(link) {
        if (link == null || link == '') return;
        link = link.split('/');
        // If the last element has a '.' (file extension)
        if (link.slice(-1)[0].includes('.')) {
            // Remove it form the array (.pop())
            // Remove the '.' and everything after it
            // Then add it back to the array (.push())
            link.push(link.pop().split('.')[0]);
        }
        console.log(link)
    }
    */


    document.querySelectorAll('#user-management .table tr')?.forEach(tableRow => {
        tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
            EditUser(tableRow?.dataset?.userid);
        });
        tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', () => {
            DeleteUser(tableRow?.dataset?.userid);
        });
    })

    function LoadPageEvents() {
        document.querySelectorAll('.input-switch')?.forEach(inputSwitch => {
            if (inputSwitch.classList.contains('events-listening')) return;
            const slider = inputSwitch.querySelector('.input-switch-slider');
            inputSwitch.querySelectorAll('.input-switch-option label')?.forEach((switchOption, index) => {
                switchOption.addEventListener('change', () => {
                    if (!switchOption?.checked == false) return;
                    slider.style.left = `${100 / inputSwitch.style.getPropertyValue('--count') * index}%`;
                });
            });
            inputSwitch.classList.add('events-listening');
        });
    }

    function EditUser(userid) {
        if (userid == null) return;
    }
    function DeleteUser(userid) {
        if (userid == null) return;
    }

    function DisplayModel(id, isModal = true, data = []) {
        if (id == null) return;
        const modal = document.getElementById(id);
        if (modal.tagName !== "DIALOG") return;
        // Clear form. (should already be cleared anyway)
        modal.querySelector('form')?.reset();
        // The default [] is still an array
        if (!Array.isArray(data)) return;
        // Loop through data that is already in each input
        data.forEach(row => {
            const input = document.getElementById(row?.id);
            if (input == null) return;
            // Radio/Checkbox specific
            if (input.type === "radio" || input.type === "checkbox") {
                if (row?.value === true) input.checked = true;
                else input.checked = false;
            } else {
                // If it doesn't have a value, just put an empty string
                input.value = row?.value ?? '';
            }
        });

        if (isModal) modal.showModal();
        else modal.show();
    }

})();