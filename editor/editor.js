// editor/editor.js  (type="module")
// Tanggung jawab: inisialisasi, binding event UI, save/load desain

import { initCanvas, addPhotoToCanvas, setFrame, ensureFrameOnTop, getCanvas } from "./canvas.js";
import { initControls, setRotation, setZoom } from "./controls.js";

document.addEventListener("DOMContentLoaded", () => {

    // ── Inisialisasi ──────────────────────────────────────────────
    initCanvas("design-canvas");
    initControls();

    // ── Upload Foto ───────────────────────────────────────────────
    const uploadInput = document.getElementById("photo-upload");
    uploadInput.addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("photo", file);

        const resp = await fetch("/api/upload-photo.php", {
            method: "POST",
            body: formData,
        });
        const data = await resp.json();

        if (data.success) {
            addPhotoToCanvas(data.url);
        } else {
            alert("Upload gagal: " + data.error);
        }
    });

    // ── Pilih Frame ───────────────────────────────────────────────
    document.querySelectorAll(".frame-option").forEach(el => {
        el.addEventListener("click", () => {
            document.querySelectorAll(".frame-option").forEach(f => f.classList.remove("active"));
            el.classList.add("active");
            setFrame(el.dataset.frameUrl);
        });
    });

    // ── Slider Rotate & Zoom ──────────────────────────────────────
    document.getElementById("rotate-slider").addEventListener("input", (e) => {
        setRotation(parseFloat(e.target.value));
    });
    document.getElementById("zoom-slider").addEventListener("input", (e) => {
        setZoom(parseFloat(e.target.value));
    });

    // ── Save Desain ───────────────────────────────────────────────
    document.getElementById("btn-save").addEventListener("click", async () => {
        const canvas     = getCanvas();
        const frameId    = document.getElementById("active-frame-id").value;

        // Export canvas sebagai PNG (frame sudah ada di layer atas)
        const exportImage = canvas.toDataURL({ format: "png", quality: 1 });
        const canvasState = canvas.toJSON();

        const resp = await fetch("/api/save-design.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ canvas_state: canvasState, frame_id: frameId, export_image: exportImage }),
        });
        const data = await resp.json();

        if (data.success) {
            alert(`Desain tersimpan! ID: ${data.design_id}`);
            // Update URL agar bisa di-reload
            history.replaceState({}, "", `?design_id=${data.design_id}`);
        } else {
            alert("Gagal simpan: " + data.error);
        }
    });

    // ── Load Desain ───────────────────────────────────────────────
    const urlParams  = new URLSearchParams(window.location.search);
    const designId   = urlParams.get("design_id");

    if (designId) loadDesign(parseInt(designId, 10));
});

async function loadDesign(designId) {
    const resp = await fetch(`/api/load-design.php?id=${designId}`);
    const data = await resp.json();
    if (!data.success) { alert("Desain tidak ditemukan."); return; }

    const canvas = getCanvas();
    canvas.loadFromJSON(data.design.canvas_state, () => {
        canvas.renderAll();
        ensureFrameOnTop();
    });
}
