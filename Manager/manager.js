document.addEventListener('DOMContentLoaded', function() {
    
    const btnSecretary = document.getElementById('toggleSecretary');
    const btnSponsorship = document.getElementById('toggleSponsorship');
    
    const sectionSecretary = document.getElementById('secretarySection');
    const sectionSponsorship = document.getElementById('sponsorshipSection');

    // Function to switch tabs
    function switchTab(activeBtn, activeSection, inactiveBtn, inactiveSection) {
        // Toggle Buttons
        activeBtn.classList.add('active');
        inactiveBtn.classList.remove('active');
        
        // Toggle Sections (with simple animation logic via CSS)
        activeSection.style.display = 'block';
        inactiveSection.style.display = 'none';
        
        // Re-trigger animation
        activeSection.style.animation = 'none';
        activeSection.offsetHeight; /* trigger reflow */
        activeSection.style.animation = 'fadeIn 0.4s ease-out';
    }

    btnSecretary.addEventListener('click', function() {
        switchTab(btnSecretary, sectionSecretary, btnSponsorship, sectionSponsorship);
    });

    btnSponsorship.addEventListener('click', function() {
        switchTab(btnSponsorship, sectionSponsorship, btnSecretary, sectionSecretary);
    });

});