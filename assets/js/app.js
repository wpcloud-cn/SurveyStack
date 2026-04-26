(function(){
  document.querySelectorAll('textarea').forEach(function(el){el.addEventListener('input',function(){this.style.height='auto';this.style.height=this.scrollHeight+'px';});});
})();
