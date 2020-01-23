<script>
    $(".form-horizontal").submit(function (event) {
        var x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
        if (x) {
            return true;
        }
        else {

            event.preventDefault();
            return false;
        }

    });
</script>
