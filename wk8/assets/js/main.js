'use strict';


function initPasswordStrength() {
  const inputs = document.querySelectorAll('[data-pw-strength]');
  inputs.forEach(input => {
    const barId = input.dataset.pwStrength;
    const fill  = document.getElementById(barId + '-fill');
    const label = document.getElementById(barId + '-label');

    input.addEventListener('input', () => {
      const pw = input.value;
      let score = 0;
      if (pw.length >= 8)           score++;  
      if (/[A-Z]/.test(pw))         score++;  
      if (/[0-9]/.test(pw))         score++; 
      if (/[^A-Za-z0-9]/.test(pw))  score++;  

      const levels = [
        { pct: '0%',   bg: '#ccc',     text: ''       },
        { pct: '25%',  bg: '#E74C3C', text: 'Weak'   },
        { pct: '50%',  bg: '#E67E22', text: 'Fair'   },
        { pct: '75%',  bg: '#F1C40F', text: 'Good'   },
        { pct: '100%', bg: '#27AE60', text: 'Strong' },
      ];
      const lvl = levels[score];
      fill.style.width      = lvl.pct;
      fill.style.background = lvl.bg;
      label.textContent     = lvl.text;
    });
  });
}


function validateField(input) {
  const val = input.value.trim();
  let err = '';

  if (input.required && !val) {
    err = 'This field is required.';
  } else if (input.type === 'email' && val
         && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
    err = 'Enter a valid email address.';
  } else if (input.dataset.minLen
         && val.length < parseInt(input.dataset.minLen)) {
    err = `Minimum ${input.dataset.minLen} characters.`;
  } else if (input.dataset.match) {
    const target = document.getElementById(input.dataset.match);
    if (target && val !== target.value.trim())
      err = 'Passwords do not match.';
  }

  const errEl = document.getElementById(input.id + '-error');
  if (errEl) {
    errEl.textContent   = err;
    errEl.style.display = err ? 'block' : 'none';
  }
  input.classList.toggle('error', !!err);
  return !err;
}


function initFormValidation() {
  document.querySelectorAll('[data-validate]').forEach(form => {
    const fields = form.querySelectorAll('input[required]');

    
    fields.forEach(f => f.addEventListener('blur',
                  () => validateField(f)));

    
    form.addEventListener('submit', e => {
      let valid = true;
      fields.forEach(f => { if (!validateField(f)) valid = false; });
      if (!valid) {
        e.preventDefault();
        const first = form.querySelector('.error');
        if (first) first.focus();
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initPasswordStrength();
  initFormValidation();
});
