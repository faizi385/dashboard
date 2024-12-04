


//Js for Scrollable cards

const scrollableContainer = document.querySelector('.scrollable-cards');

let isDown = false;
let startX;
let scrollLeft;

scrollableContainer.addEventListener('mousedown', (e) => {
    isDown = true;
    scrollableContainer.classList.add('active');
    startX = e.pageX - scrollableContainer.offsetLeft;
    scrollLeft = scrollableContainer.scrollLeft;
});

scrollableContainer.addEventListener('mouseleave', () => {
    isDown = false;
    scrollableContainer.classList.remove('active');
});

scrollableContainer.addEventListener('mouseup', () => {
    isDown = false;
    scrollableContainer.classList.remove('active');
});

scrollableContainer.addEventListener('mousemove', (e) => {
    if (!isDown) return; // Stop execution if not dragging
    e.preventDefault();
    const x = e.pageX - scrollableContainer.offsetLeft;
    const walk = (x - startX) * 2; // Adjust the scroll speed
    scrollableContainer.scrollLeft = scrollLeft - walk;
});
document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.pickable-card');

        cards.forEach(card => {
            card.addEventListener('click', () => {
                card.classList.toggle('selected');
            });
        });
    });

    //Js for Scrollable cards



    