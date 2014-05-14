$(function ()
{
    $('#form-login').submit(function (e) {
        e.preventDefault();

        var $this = $(e.currentTarget),
            inputs = {};

        // Send all form's inputs
        $.each($this.find('input'), function (i, item) {
            var $item = $(item);
            inputs[$item.attr('name')] = $item.val();
        });

        // Send form into ajax
        $.ajax({
            url : $this.attr('action'),
            type : 'POST',
            dataType : 'json',
            data : inputs,
            success : function (data) {
                alert("OK");
                if (data.has_error) {
                    // Insert your error process
                    alert(data.error);
                }
                else {
                    // Insert your success process
                    alert('Welcome ' + data.username + ' !');
                    window.location = data.target_path;
                }
            },
            failure : function(data)
            {
                alert("FEHLER");
            }
        });
    });
});