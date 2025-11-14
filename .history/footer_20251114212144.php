
    <footer class="pt-4 mt-4 border-top">
      <div class="d-flex justify-content-between">
        <div>© HM Official - Multimedia</div>
        <div>Built with Bootstrap 5</div>
      </div>
    </footer>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Auto-submit filter forms when inputs change.
  // - Debounce text-like inputs to avoid rapid submits
  // - Use both 'input' and 'change' for maximum compatibility
  // - Ignore buttons and inputs with data-no-auto attribute
  document.querySelectorAll('form.auto-filter').forEach(function(form){
    var elements = Array.from(form.querySelectorAll('input[name], select[name], textarea[name]'))
      .filter(function(el){ return el.type !== 'submit' && el.type !== 'button' && el.dataset.noAuto === undefined; });

    elements.forEach(function(el){
      var isTextLike = el.tagName.toLowerCase() === 'input' && ['text','search','month','date','number'].indexOf(el.type) !== -1 || el.tagName.toLowerCase() === 'textarea';

      var submitNow = function(){
        try{ form.submit(); }catch(e){/* fail silently */}
      };

      if(isTextLike){
        var debounced = function(){ if(el._autoTimer) clearTimeout(el._autoTimer); el._autoTimer = setTimeout(submitNow, 350); };
        el.addEventListener('input', debounced);
        // also submit on change for some browsers
        el.addEventListener('change', debounced);
        // submit on Enter key immediately
        el.addEventListener('keydown', function(ev){ if(ev.key === 'Enter'){ ev.preventDefault(); submitNow(); } });
      } else {
        // selects, checkboxes, radios -> submit on change
        el.addEventListener('change', submitNow);
      }
    });
  });
});
</script>
</body>
</html>