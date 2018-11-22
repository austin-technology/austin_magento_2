/**
 * Created by thaivh on 23/3/17.
 */
require(
    [
        'jquery',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($,fsl) {
        $('button[id^="del-"]').click(function () {
            if (confirm("Delete this card ?")) {
                var id = $(this).val();
                $(this).attr("style","display:none");
                $('#loading-'+id).prop('hidden', false);
                // console.log(window.delUrl);
                $.ajax({
                    type: 'POST',
                    url: window.delUrl,
                    dataType: "json",
                    async: false,
                    data: {
                        id: id
                    },
                    success: function (response) {
                        if (response.code=="ok") {
                            alert("Success!");
                            location.reload();
                        }
                        else {
                            alert(response.mess)
                            location.reload();
                        }
                    },
                    error: function () {
                        alert("Has something wrong while deleting your card !");
                        location.reload();
                    }
                })
            }
        }
    )
})
