// editor/canvas.js
// Tanggung jawab: inisialisasi canvas, render foto, kelola frame overlay

const CANVAS_WIDTH  = 1181;   // 10cm @ 300dpi
const CANVAS_HEIGHT = 1772;   // 15cm @ 300dpi

let canvas = null;
let activeFrame = null;

export function initCanvas(canvasElementId) {
    canvas = new fabric.Canvas(canvasElementId, {
        width:           CANVAS_WIDTH,
        height:          CANVAS_HEIGHT,
        backgroundColor: "#ffffff",
        selection:       true,
        preserveObjectStacking: true,
    });
    return canvas;
}

export function getCanvas() { return canvas; }

// Render foto ke canvas — default center, origin center
export function addPhotoToCanvas(url) {
    fabric.Image.fromURL(url, (img) => {
        // Scaling proporsional agar foto masuk canvas
        const scaleX = CANVAS_WIDTH  / img.width;
        const scaleY = CANVAS_HEIGHT / img.height;
        const scale  = Math.min(scaleX, scaleY) * 0.9; // 90% canvas

        img.set({
            left:          CANVAS_WIDTH  / 2,
            top:           CANVAS_HEIGHT / 2,
            originX:       "center",
            originY:       "center",
            scaleX:        scale,
            scaleY:        scale,
            lockScalingFlip: true,         // tidak boleh flip negatif
            hasRotatingPoint: true,
            cornerColor:   "#3B82F6",
            cornerSize:    16,
            transparentCorners: false,
        });

        canvas.add(img);
        canvas.setActiveObject(img);

        // Pastikan frame selalu di atas foto
        if (activeFrame) canvas.bringToFront(activeFrame);
        canvas.renderAll();
    }, { crossOrigin: "anonymous" });
}

// Load frame PNG transparan sebagai overlay tidak-selectable
export function setFrame(frameUrl) {
    // Hapus frame lama jika ada
    if (activeFrame) {
        canvas.remove(activeFrame);
        activeFrame = null;
    }
    if (!frameUrl) { canvas.renderAll(); return; }

    fabric.Image.fromURL(frameUrl, (frame) => {
        frame.set({
            left:          0,
            top:           0,
            originX:       "left",
            originY:       "top",
            scaleX:        CANVAS_WIDTH  / frame.width,
            scaleY:        CANVAS_HEIGHT / frame.height,
            selectable:    false,
            evented:       false,
            lockMovementX: true,
            lockMovementY: true,
            hasControls:   false,
            hasBorders:    false,
        });

        activeFrame = frame;
        canvas.add(frame);
        canvas.bringToFront(frame);
        canvas.renderAll();
    }, { crossOrigin: "anonymous" });
}

// Pastikan frame kembali di atas setelah load JSON
export function ensureFrameOnTop() {
    if (activeFrame) canvas.bringToFront(activeFrame);
    canvas.renderAll();
}
