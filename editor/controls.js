// editor/controls.js
// Tanggung jawab: konfigurasi kontrol transform, batasan boundary

import { getCanvas } from "./canvas.js";

const CANVAS_WIDTH  = 1181;
const CANVAS_HEIGHT = 1772;
const SCALE_MIN = 0.1;
const SCALE_MAX = 5.0;

export function initControls() {
    const canvas = getCanvas();

    // Batasi scaling negatif dan boundary canvas
    canvas.on("object:scaling", (e) => {
        const obj = e.target;
        if (!obj) return;

        // Clamp scale agar tidak negatif / terlalu kecil
        if (obj.scaleX < SCALE_MIN) obj.scaleX = SCALE_MIN;
        if (obj.scaleY < SCALE_MIN) obj.scaleY = SCALE_MIN;
        if (obj.scaleX > SCALE_MAX) obj.scaleX = SCALE_MAX;
        if (obj.scaleY > SCALE_MAX) obj.scaleY = SCALE_MAX;
    });

    // Batasi movement agar tidak terlalu jauh dari canvas
    canvas.on("object:moving", (e) => {
        const obj    = e.target;
        const bound  = obj.getBoundingRect(true);
        const margin = 50; // boleh keluar 50px dari tepi

        if (bound.left > CANVAS_WIDTH - margin) {
            obj.left = CANVAS_WIDTH - margin + (obj.left - bound.left);
        }
        if (bound.top > CANVAS_HEIGHT - margin) {
            obj.top = CANVAS_HEIGHT - margin + (obj.top - bound.top);
        }
        if (bound.left + bound.width < margin) {
            obj.left = margin - bound.width + (obj.left - bound.left);
        }
        if (bound.top + bound.height < margin) {
            obj.top = margin - bound.height + (obj.top - bound.top);
        }
    });
}

// Utility: rotate objek aktif dari slider
export function setRotation(angleDeg) {
    const canvas = getCanvas();
    const obj = canvas.getActiveObject();
    if (!obj) return;
    obj.set("angle", angleDeg);
    canvas.renderAll();
}

// Utility: zoom objek aktif dari slider
export function setZoom(scaleFactor) {
    const canvas = getCanvas();
    const obj = canvas.getActiveObject();
    if (!obj) return;
    obj.scale(scaleFactor);
    canvas.renderAll();
}
