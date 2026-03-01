// public/assets/js/designer.js

function showToast(msg, duration = 2200) {
  let t = document.getElementById('designerToast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'designerToast';
    t.className = 'designer-toast';
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), duration);
}
