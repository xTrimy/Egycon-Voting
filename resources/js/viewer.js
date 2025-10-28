import Viewer from 'viewerjs';
import 'viewerjs/dist/viewer.css';

document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.viewer-image');
    images.forEach(function (image) {
        new Viewer(image, {
            toolbar: true,
            navbar: false,
            title: false,
            movable: false,
            zoomable: true,
            scalable: false,
            rotatable: false,
            transition: true,
        });
    });
});
