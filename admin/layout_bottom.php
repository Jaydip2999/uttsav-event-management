</div> <!-- main -->
</div> <!-- layout -->

<script>
const toggleBtn = document.getElementById("toggleBtn");
const sidebar = document.getElementById("sidebar");

if(toggleBtn){
    toggleBtn.addEventListener("click", () => {

        if(window.innerWidth <= 768){
            sidebar.classList.toggle("active");
        } else {
            sidebar.classList.toggle("collapsed");
        }

    });
}
</script>

</body>
</html>
