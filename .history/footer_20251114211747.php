
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
  // Auto-submit filter forms when inputs change. Text inputs debounce to avoid rapid submits.
  document.querySelectorAll('form.auto-filter').forEach(function(form){
    form.querySelectorAll('input[name], select[name]').forEach(function(el){
      var eventName = (el.tagName.toLowerCase() === 'input' && (el.type === 'text' || el.type === 'search' || el.type === 'month')) ? 'input' : 'change';
      var handler = function(){
        if(eventName === 'input'){
          if(el._debounce) clearTimeout(el._debounce);
          el._debounce = setTimeout(function(){ form.submit(); }, 400);
        } else {
          form.submit();
        }
      };
      el.addEventListener(eventName, handler);
    });
    // Also submit when pressing Enter on any input inside the form
    form.addEventListener('submit', function(e){ /* normal submit allowed */ });
  });
});
</script>
</body>
</html>