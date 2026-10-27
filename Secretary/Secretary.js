document.addEventListener('DOMContentLoaded', function() {
    
    const btnVolunteers = document.getElementById('toggleVolunteers');
    const btnHire = document.getElementById('toggleHireEmployees');
    
    const sectionVolunteers = document.getElementById('volunteersSection');
    const sectionHire = document.getElementById('hireEmployeesSection');

    function switchTab(activeBtn, activeSection, inactiveBtn, inactiveSection) {
        // Toggle Buttons
        activeBtn.classList.add('active');
        inactiveBtn.classList.remove('active');
        
        // Toggle Sections
        activeSection.style.display = 'block';
        inactiveSection.style.display = 'none';
        
        // Animation
        activeSection.style.animation = 'none';
        activeSection.offsetHeight; 
        activeSection.style.animation = 'fadeIn 0.4s ease-out';
    }

    btnVolunteers.addEventListener('click', function() {
        switchTab(btnVolunteers, sectionVolunteers, btnHire, sectionHire);
    });

    btnHire.addEventListener('click', function() {
        switchTab(btnHire, sectionHire, btnVolunteers, sectionVolunteers);
    });

});