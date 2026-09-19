/**
 * HidroGest - Alternar modo claro / oscuro.
 * La preferencia se persiste en localStorage.
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'hidrogest-theme';
  var root = document.documentElement;

  function getStored() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch (e) {
      return null;
    }
  }

  function store(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {
      /* localStorage no disponible: ignorar */
    }
  }

  function apply(theme) {
    root.setAttribute('data-bs-theme', theme === 'dark' ? 'dark' : 'light');
  }

  function syncButton() {
    var dark = root.getAttribute('data-bs-theme') === 'dark';
    var moon = document.getElementById('themeIconMoon');
    var sun = document.getElementById('themeIconSun');
    if (moon) moon.style.display = dark ? 'none' : '';
    if (sun) sun.style.display = dark ? '' : 'none';
  }

  var toggle = document.getElementById('themeToggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      apply(next);
      store(next);
      syncButton();
    });
  }

  var initial = getStored();
  if (initial) {
    apply(initial);
  } else {
    apply('light');
  }

  syncButton();
})();