document.addEventListener("DOMContentLoaded", function(event) { 
    const button = document.getElementById('scroll-top');
    if (button) {
        // button.style.backgroundColor = "gray"; // Change button background color
        const svgIcon = button.querySelector('svg path');
        if (svgIcon) {
            svgIcon.style.fill = "gray"; // Change SVG icon color
        }
    }
});