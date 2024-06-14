document.addEventListener("DOMContentLoaded", function() {
    const images = document.querySelectorAll(".visualization-box img");
    images.forEach(img => {
        img.onerror = function() {
            const parent = img.parentElement;
            const message = document.createElement("p");
            message.textContent = img.alt + " tidak tersedia.";
            parent.appendChild(message);
            img.style.display = "none";
        }
        img.src = img.getAttribute("data-src");
    });
});
