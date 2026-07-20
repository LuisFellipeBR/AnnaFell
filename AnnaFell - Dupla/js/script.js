// script.js - Funções JavaScript para toda a loja

document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("modalContato");
    var btn = document.getElementById("abrirContato");
    
    if (btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }
    
    var span = document.getElementsByClassName("fechar-modal");
    if (span.length > 0) {
        span[0].onclick = function() {
            modal.style.display = "none";
        }
    }
    
    var btnFechar = document.getElementsByClassName("fechar-modal-btn");
    if (btnFechar.length > 0) {
        btnFechar[0].onclick = function() {
            modal.style.display = "none";
        }
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});