<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.f
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
    get_template_part( 'template-parts/footer' );
}
?>

<?php wp_footer(); ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA==" crossorigin="anonymous"></script>
<script>

jQuery(document).ready(function($) {
    cart_improvement_functions();
    cart_dropdown_improvement();
    track_ajax_add_to_cart();
    if (jQuery.fn.avia_sc_slider) {
        jQuery(".shop_slider_yes ul").avia_sc_slider({
            appendControlls: false,
            group: true,
            slide: '.product',
            arrowControll: true,
            autorotationInterval: 'parent'
        });
    }
    product_add_to_cart_click();
    quantity_selector_button_mod();
    setTimeout(first_load_amount, 10);
    $('body').bind('added_to_cart', update_cart_dropdown);
    jQuery('.avia_mobile .sort-param').on('touchstart', function() {});
});
jQuery(document).bind("updated_wc_div", function() {
    setTimeout(update_cart_dropdown, 1000);
    quantity_selector_button_mod();
});
function quantity_selector_button_mod() {
    jQuery(".quantity input[type=number]").each(function() {
        var number = jQuery(this)
          , max = parseFloat(number.attr('max'))
          , min = parseFloat(number.attr('min'))
          , step = parseInt(number.attr('step'), 10)
          , newNum = jQuery(jQuery('<div />').append(number.clone(true)).html().replace('number', 'text')).insertAfter(number);
        number.remove();
        setTimeout(function() {
            if (newNum.next('.plus').length == 0) {
                var minus = jQuery('<input type="button" value="-" class="minus">').insertBefore(newNum)
                  , plus = jQuery('<input type="button" value="+" class="plus">').insertAfter(newNum);
                minus.on('click', function() {
                    var the_val = parseInt(newNum.val(), 10) - step;
                    the_val = the_val < 0 ? 0 : the_val;
                    the_val = the_val < min ? min : the_val;
                    newNum.val(the_val);
                    enable_update_cart_button();
                });
                plus.on('click', function() {
                    var the_val = parseInt(newNum.val(), 10) + step;
                    the_val = the_val > max ? max : the_val;
                    newNum.val(the_val);
                    enable_update_cart_button();
                });
            }
        }, 10);
    });
}
function update_cart_dropdown(event) {
    var the_html = jQuery('html')
      , menu_cart = jQuery('.cart_dropdown')
      , cart_counter = jQuery('.cart_dropdown#menu-item-shop .av-cart-counter')
      , empty = menu_cart.find('.empty')
      , msg_success = menu_cart.data('success')
      , product = jQuery.extend({
        name: "Product",
        price: "",
        image: ""
    }, avia_clicked_product)
      , counter = 0;
    if (!empty.length) {
        the_html.addClass('html_visible_cart');
    }
    if (typeof event != 'undefined') {
        var header = jQuery('.html_header_sticky #header_main .cart_dropdown_first, .html_header_sidebar #header_main .cart_dropdown_first')
          , oldTemplates = jQuery('.added_to_cart_notification').trigger('avia_hide')
          , template = jQuery("<div class='added_to_cart_notification'><span class='avia-arrow'></span><div class='added-product-text'><strong>\"" + product.name + "\"</strong> " + msg_success + "</div> " + product.image + "</div>").css('opacity', 0);
        if (!header.length)
            header = 'body';
        template.bind('mouseenter avia_hide', function() {
            template.animate({
                opacity: 0,
                top: parseInt(template.css('top'), 10) + 15
            }, function() {
                template.remove();
            });
        }).appendTo(header).animate({
            opacity: 1
        }, 500);
        setTimeout(function() {
            template.trigger('avia_hide');
        }, 2500);
    }
    menu_cart.find('.cart_list li .quantity').each(function() {
        counter += parseInt(jQuery(this).text(), 10);
    });
    console.log(counter);
    if (cart_counter.length && counter > 0) {
        cart_counter.removeClass('av-active-counter');
        setTimeout(function() {
            cart_counter.addClass('av-active-counter').text(counter);
        }, 10);
    }
}
var avia_clicked_product = {};
function track_ajax_add_to_cart() {
    jQuery('body').on('click', '.add_to_cart_button', function() {
        var productContainer = jQuery(this).parents('.product').eq(0)
          , product = {};
        product.name = productContainer.find('.inner_product_header h3').text();
        product.image = productContainer.find('.thumbnail_container img');
        product.price = productContainer.find('.price .amount').last().text();
        if (productContainer.length == 0) {
            productContainer = jQuery(this);
            product.name = productContainer.find('.av-cart-update-title').text();
            product.image = productContainer.find('.av-cart-update-image');
            product.price = productContainer.find('.av-cart-update-price').text();
        }
        if (product.image.length) {
            product.image = "<img class='added-product-image' src='" + product.image.get(0).src + "' title='' alt='' />";
        } else {
            product.image = "";
        }
        avia_clicked_product = product;
    });
}
function first_load_amount() {
    var counter = 0
      , limit = 15
      , ms = 500
      , check = function() {
        var new_total = jQuery('.cart_dropdown .dropdown_widget_cart:eq(0) .total .amount');
        if (new_total.length) {
            update_cart_dropdown();
        } else {
            counter++;
            if (counter < limit) {
                setTimeout(check, ms);
            }
        }
    };
    check();
    if (jQuery('.av-display-cart-on-load').length && jQuery('.woocommerce-message').length == 0) {
        var dropdown = jQuery('.cart_dropdown');
        setTimeout(function() {
            dropdown.trigger('mouseenter');
        }, 500);
        setTimeout(function() {
            dropdown.trigger('mouseleave');
        }, 2500);
    }
}
function product_add_to_cart_click() {
    var jbody = jQuery('body')
      , catalogue = jQuery('.av-catalogue-item')
      , loader = false;
    if (catalogue.length)
        loader = jQuery.avia_utilities.loading();
    jbody.on('click', '.add_to_cart_button', function() {
        var button = jQuery(this);
        button.parents('.product:eq(0)').addClass('adding-to-cart-loading').removeClass('added-to-cart-check');
        if (button.is('.av-catalogue-item')) {
            loader.show();
        }
    })
    jbody.bind('added_to_cart', function() {
        jQuery('.adding-to-cart-loading').removeClass('adding-to-cart-loading').addClass('added-to-cart-check');
        if (loader !== false) {
            loader.hide();
        }
    });
}
function cart_improvement_functions() {
    jQuery('.product_type_downloadable, .product_type_virtual').addClass('product_type_simple');
    jQuery('.woocommerce-tabs .tabs a').addClass('no-scroll');
    jQuery('.single-product-main-image>.images a').attr('rel', 'product_images[grouped]');
}
function cart_dropdown_improvement() {
    var dropdown = jQuery('.cart_dropdown')
      , subelement = dropdown.find('.dropdown_widget').css({
        display: 'none',
        opacity: 0
    });
    dropdown.hover(function() {
        subelement.css({
            display: 'block'
        }).stop().animate({
            opacity: 1
        });
    }, function() {
        subelement.stop().animate({
            opacity: 0
        }, function() {
            subelement.css({
                display: 'none'
            });
        });
    });
}
function enable_update_cart_button() {
    var $update_cart_button = jQuery('table.shop_table.cart').closest('form').find('input[name="update_cart"]');
    if ($update_cart_button.length) {
        $update_cart_button.prop('disabled', false);
    }
};

function verifyMonth(month) {
    switch (month) {
        case "01":
            return 'Janeiro';
            break;
        case "02":
            return 'Fevereiro';
            break;
        case "03":
            return 'Março';
            break;
        case "04":
            return 'Abril';
            break;
        case "05":
            return 'Maio';
            break;
        case "06":
            return 'Junho';
            break;
        case "07":
            return 'Julho';
            break;
        case "08":
            return 'Agosto';
            break;
        case "09":
            return 'Setembro';
            break;
        case "10":
            return 'Outubro';
            break;
        case "11":
            return 'Novembro';
            break;
        default:
            return 'Dezembro';
            break;
    }
};

// INICIA - Codes Forms Orçamentos
jQuery(document).ready(function($) {

    var firstClick = true;

    <?php // WhatsApp
    $whatsapp = get_option('whatsapp');
    if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsapp,  $matches)) {
        $whatsappVisual = '(' . $matches[2] . ') ' . $matches[3] . '-' . $matches[4];
    }
    ?>


    <?php // INICIA O IF - FX5 ?>
    <?php if (has_term('FX5', 'product_cat')) { ?>

        jQuery('#tituloproduto').clone().appendTo(jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3'));
        jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3 #tituloproduto').addClass('mobile');

        $('button.single_add_to_cart_button.button.alt').css('padding', '12px 25px 12px 101px');
        $('button.single_add_to_cart_button.button.alt').css('font-size', '21px');

        if (jQuery("div#gform_confirmation_message_15").length) {
            firstClick = false;
            $('.area-form-produto').css('display', 'block');
            $('button.single_add_to_cart_button.button.alt').html("ADICIONAR AO CARRINHO");
            $('.single_add_to_cart_button').html('COMPRAR');
            jQuery(".above_cart_button").css('display', 'block');
        }

        $('button.single_add_to_cart_button.button.alt').html("CALCULAR VALOR");


        $('#gform_submit_button_15').click(function() {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            var valorT = jQuery('span#valor-total').html();
            var valorTra = jQuery('span#valor-traseiro').html();
            var valorDia = jQuery('span#valor-dianteiro').html();

            if (((controleT == 0) && (controleD == 0)) || (valorT == 0) || ((valorTra == 0) && (valorDia == 0))) {
                alert("Você precisa selecionar uma quantidade diferente de 0");
                return false;
            }

            // BOTÃO ENVIAR WHATSAPP
            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor')) { ?>
                jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"></div>');
                //jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"><button id="gerawhats">GERAR LINK PARA WHATSAPP</button></div>');
                //jQuery(document).on('click', '#gerawhats', function(){

                <?php
                $whatsappX = get_option('whatsapp');
                if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsappX,  $matches)) {
                    $whatsappVisualX = $matches[2] . ' ' . $matches[3] . '.' . $matches[4];
                }
                ?>
                var whatsapp1 = '<?php echo $whatsappX; ?>';
                var whatsapp2 = '<?php echo $whatsappVisualX; ?>';

                var infoDate = new Date();
                var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();

                var cliente         = jQuery("#input_15_1").val();
                var emailcliente    = jQuery("#input_15_3").val();
                var telcliente      = jQuery("#input_15_2").val();
                var veiculo         = jQuery("#input_15_6").val();
                var nomeDianteiro   = jQuery("#input_15_17").val();
                var valorDianteiro  = jQuery("#input_15_15").val();
                var nomeTraseiro    = jQuery("#input_15_18").val();
                var valorTraseiro   = jQuery("#input_15_16").val();

                var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                var tamDianteiro = 'Tamanho Original';
                var tamTraseiro = 'Tamanho Original';

                <?php if (has_term('configuravel', 'product_cat')) { ?>
                    var tamDianteiro = (jQuery('.tamanhodianteiro').find(":selected").text());
                    var tamTraseiro = (jQuery('.tamanhotraseiro').find(":selected").text());
                <?php } ?>

                var valortotal = jQuery('span#valor-total').html().replace(",", ".");
                var valortotal = Number(valortotal);
                var vardesconto = <?php echo get_option('descontoavista'); ?>;
                var desconto = '(' + vardesconto + '% de desconto)';
                var totalDesconto = (valortotal - (valortotal * (vardesconto / 100))).toFixed(2).replace(".", ",");
                var condicoes = jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '|');
                var condicoes = condicoes.replace(/(\r\n|\n|\r)/gm, "")
                var condicoes = condicoes.replace(/(<([^>]+)>)/ig, "");

                var valortotal = valortotal.toFixed(2).replace(".", ",");

                if (qtDianteiro > 0) {
                    var dianteiroProduto = 1;
                } else {
                    var dianteiroProduto = 0;
                }
                if (qtTraseiro > 0) {
                    var traseiroProduto = 1;
                } else {
                    var traseiroProduto = 0;
                }

                var long_url = '?tipo=fx5&ntipo=Amortecedor Original&tl=' + jQuery("#input_15_13").val() + '&valor2=' + valortotal + '&ti=' + veiculo + '&dP=' + dianteiroProduto + '&nD=' + nomeDianteiro + '&qD=' + qtDianteiro + '&tD=' + tamDianteiro + '&vD=' + valorDianteiro + '&tP=' + traseiroProduto + '&nT=' + nomeTraseiro + '&qT=' + qtTraseiro + '&tT=' + tamTraseiro + '&vT=' + valorTraseiro + '&d=' + myDateString + '&c=' + jQuery("#input_15_1").val() + '&mail=' + jQuery("#input_15_3").val() + '&des=' + desconto + '&todes=' + totalDesconto + '&w1=' + whatsapp1 + '&w2=' + whatsapp2 + '&im1=' + imagem1 + '&im2=' + imagem2 + '&link=<?php echo $current_url; ?>' + '&con=' + condicoes;

                console.log(long_url);

                long_url = long_url.replace(/\s/g, "%20");



                // CRIA LINK REDUZIDO
                let linkRequest = {
                    destination: 'https://offlimits.com.br/orcamento-padrao/' + long_url,
                    domain: {
                        fullName: "rebrand.ly"
                    }
                }
                let requestHeaders = {
                    "Content-Type": "application/json",
                    "apikey": "f1c72905e5764ea1bdf718eec7c26f07",
                }
                $.ajax({
                    url: "https://api.rebrandly.com/v1/links",
                    type: "post",
                    data: JSON.stringify(linkRequest),
                    headers: requestHeaders,
                    dataType: "json",
                    success: (link) => {
                        console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
                        var long_url = 'https://' + (`${link.shortUrl}`);
                        console.log('link reduzido: ' + long_url);


                        // CRIA TEXTO DO WHATSAPP

                        var txtTraseito = '';
                        var txtdianteiro = '';
                        if (qtTraseiro > '0') {
                            var txtTraseito = "• *[" + qtTraseiro + "]* " + nomeTraseiro + " - " + tamTraseiro + " | R$ " + valorTraseiro + " un. \n";
                        }
                        if (qtDianteiro > '0') {
                            var txtdianteiro = "• *[" + qtDianteiro + "]* " + nomeDianteiro + " - " + tamDianteiro + " | R$ " + valorDianteiro + " un. \n";
                        }


                        var texto = "Olá, " + cliente + "\n\n Obrigado pelo seu interesse por nossos produtos. Segue abaixo, o orçamento conforme informações fornecidas. A efetivação da sua compra pode ser feita diretamente no site ou por telefone.\n\n ------\n\n*ORÇAMENTO*\n• VEÍCULO: " + veiculo + "\n• ORÇAMENTO EMITIDO EM: " + myDateString + "\n\n" + txtTraseito + " " + txtdianteiro + "\n • *TOTAL:* R$ " + totalDesconto + ' ' + desconto + "\n\n VEJA SEU ORÇAMENTO COMPLETO: " + long_url;


                        texto = window.encodeURIComponent(texto);
                        var urlWhatsapp = 'https://web.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;
                        var urlWhatsapp2 = 'https://api.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;

                        // MONTA E EXIBE BOTÕES
                        jQuery('#Enviawhatsapp').remove();
                        jQuery('#Enviawhatsapp2').remove();
                        jQuery('.gerawhats').append('<a target="_blank" class="desktop" id="Enviawhatsapp">ENVIAR POR WHATSAPP</a>');
                        jQuery('.gerawhats').append('<a target="_blank" class="mobile" id="Enviawhatsapp2">ENVIAR POR WHATSAPP</a>');
                        $('#Enviawhatsapp').attr('href', urlWhatsapp);
                        $('#Enviawhatsapp2').attr('href', urlWhatsapp2);

                    }
                }); // END REBRAND
                //              });
            <?php } ?>
            // END WHATSAPP

        });// END $('#gform_submit_button_15').click(function()


        jQuery("ul#gform_fields_15 input").change(function() {
            jQuery('.comprawhats2').addClass('comprawhats');
            jQuery('input#input_59_1').val(jQuery('input#input_15_1').val());
            jQuery('input#input_59_3').val(jQuery('input#input_15_2').val());
            jQuery('input#input_59_2').val(jQuery('input#input_15_3').val());
        });


        $('button.single_add_to_cart_button.button').click(function(event) {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            if ((controleT != 0) || (controleD != 0)) {
                if (firstClick) {
                    event.preventDefault();

                    $('.area-form-produto').css('display', 'block');
                    $('button.single_add_to_cart_button.button').css('display', 'none');
                    $('form.cart').addClass('no-after');
                    var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                    var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                    var quantidadeDianteiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val()).slice(-2);
                    var quantidadeTraseiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val()).slice(-2);
                    var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                    var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();

                    var tot = jQuery('span#valor-total').html()
                    var des = jQuery('span#valor-desconto').html();
                    tot = tot.replace(',', '.')
                    tot = Number(tot).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    <?php global $wp;
                    $current_url = home_url(add_query_arg(array(), $wp->request)); ?>

                    //                          var Tipocalibragem = $( "select.tipocalibragem option:selected" ).val();
                    <?php if (has_term('configuravel', 'product_cat')) { ?>
                        var Tdianteiro = $("select.tamanhodianteiro option:selected").val();
                        var Ttraseiro = $("select.tamanhotraseiro option:selected").val();
                    <?php } ?>

                    var link = '<?php echo $current_url; ?>?t=' + qtTraseiro + '&d=' + qtDianteiro<?php if (has_term('configuravel', 'product_cat')) { ?> + '&tt=' + Ttraseiro + '&td=' + Tdianteiro<?php } ?>;

                    jQuery('#input_15_19').val(link);


                    //var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                    //var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                    var imagem1 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide.swiper-slide-active > div > a.jet-woo-product-gallery__image-link > img').src;

                    var imagem2 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.swiper-slide.swiper-slide-next > div > a.jet-woo-product-gallery__image-link > img').src;

                    jQuery('#input_15_20').val(imagem1);
                    jQuery('#input_15_21').val(imagem2);



                    jQuery("#input_15_24").html(
                        jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '\n')
                    );

                    var valdesconto = jQuery('.colunaprecocondicaoobs').html();
                    jQuery('#input_15_23').val(valdesconto);


                    jQuery("#input_15_27").val('<?php echo $whatsappVisual; ?>');
                    jQuery("#input_15_26").val('<?php echo $whatsapp; ?>');



                    jQuery('#input_15_8').val(quantidadeDianteiro);
                    jQuery('#input_15_9').val(quantidadeTraseiro);
                    // jQuery("#input_15_10").val(jQuery('select.wccpf-field[name=opes_de_calibragem] option:selected').html());
                    jQuery("#input_15_11").val(jQuery('select.wccpf-field[name=selecione_o_tipo_de_amortecedor] option:selected').html());
                    jQuery("#input_15_12").val(jQuery('#valor-total').html());
                    jQuery("#input_15_13").val(des);

                    var tras = jQuery('span#valor-traseiro').html();
                    var dia = jQuery('span#valor-dianteiro').html();

                    tras = Number(tras).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });
                    dia = Number(dia).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    var infoDate = new Date();
                    var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                    jQuery("#input_15_14").val(myDateString);
                    jQuery("#input_15_15").val(jQuery('#valor-dianteiro').html());
                    jQuery("#input_15_16").val(jQuery('#valor-traseiro').html());


                    jQuery("#input_15_25").val('<?php if (get_option('fretegratis')) {
                        echo 'GRÁTIS';
                    } else {
                        echo 'continuar a compra para calcular';
                    } ?>');

                    jQuery("#input_15_17").val((jQuery('input.titulo_produto_dianteiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhodianteiro').find(":selected").text()) <?php } ?>);
                    jQuery("#input_15_18").val((jQuery('input.titulo_produto_traseiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhotraseiro').find(":selected").text()) <?php } ?>);
                    $('button.single_add_to_cart_button.button.alt').html('ADICIONAR AO CARRINHO');

                    var position = $("#gform_fields_15").offset().top - 120;
                    $('html, body').animate({
                        scrollTop: position
                    }, 300);

                    console.log('clicou 15');
                } // Fim - if (firstClick)
                } else {
                    alert("Você precisa selecionar uma quantidade diferente de 0");
                    return false;
                }
            });

            jQuery(document).bind('gform_confirmation_loaded', function(event, formId) {
                if (formId == 15) {
                    console.log('clicou para enviar o form');
                    $('.comprawhats').css('display', 'inline-block');

                    $('.area-form-produto').css('display', 'block');
                    firstClick = false;
                    var deslocamento = $('button.single_add_to_cart_button.button').offset().top;
                    // $('html, body').animate({
                    //    scrollTop: deslocamento
                    // }, 'slow');

                    if ($("div#gform_confirmation_message_15").length) {
                        $('button.single_add_to_cart_button.button.alt').css('display', 'block');
                        $('form.cart').removeClass('no-after');
                        $('.valorescompra').css('display', 'block');
                        $('button.single_add_to_cart_button.button.alt').addClass('cart_fx5');
                        $('.single_add_to_cart_button').html('COMPRAR');
                        $(".above_cart_button").css('display', 'block');
                        //                          gtag_report_conversion();

                        var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                        var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                        var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                        var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                        $.cookie('aberto', 'sim', {
                            expires: 3
                        });
                        $.cookie('dianteiro', qtDianteiro, {
                            expires: 3
                        });
                        $.cookie('traseiro', qtTraseiro, {
                            expires: 3
                        });

                        var deslocamento = jQuery('.valorescompra.honey-size').offset().top - 120;
                        $('html, body').animate({
                            scrollTop: deslocamento
                        }, 300);

                    } // Fim - if ($("div#gform_confirmation_message_15").length)
                } // Fim - if (formId == 15)
            }); // Fim - jQuery(document).bind('gform_confirmation_loaded', function(event, formId)

            // auto preenchimento
            var cookieaberto = $.cookie('aberto');
            var cookiedianteiro = $.cookie('dianteiro');
            var cookietraseiro = $.cookie('traseiro');

            console.log(cookieaberto);
            console.log(cookiedianteiro);
            console.log(cookietraseiro);

            <?php if (has_term('configuravel', 'product_cat')) {
                $aberto = 'configuravel'; ?> console.log('configuravel');
            <?php } else { ?> console.log('nao configuravel');
            <?php } ?>

            <?php
            $aberto = $_GET['a'];
            global $woocommerce;
            $count = $woocommerce->cart->cart_contents_count;
            if ($count > 0) {
                $aberto = 'comprador'; ?> console.log('carrinho cheio');
            <?php  } else {  ?> console.log('carrinho vazio1');
            <?php } ?>

            <?php if ($aberto) { ?>

                firstClick = false;
                $('.area-form-produto').css('display', 'none');
                $('button.single_add_to_cart_button.button').css('display', 'block');
                $('button.single_add_to_cart_button.button').addClass("botinativo");
                $('button.single_add_to_cart_button.button.alt').html("SELECIONE A QUANTIDADE DE PRODUTOS");
                $(".quantity").click(function() {
                    $('button.single_add_to_cart_button.button').removeClass("botinativo");
                    $('.valorescompra').css('display', 'block');
                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');
                });

            <?php }; ?>



            <?php

            if (($_GET['t']) || ($_GET['d'])) {
                $woocommerce->cart->empty_cart();
                $predianteiro = $_GET['d'];
                $pretraseiro = $_GET['t'];
                // $calibragem = $_GET['c'];
                $tTraseiro = $_GET['tt'];
                $tDianteiro = $_GET['td'];
            ?>
                $.removeCookie('dianteiro');
                $.removeCookie('traseiro');
            <?php

            } else {
                if (isset($_COOKIE['aberto'])) {
                    $predianteiro = $_COOKIE['dianteiro'];
                    $pretraseiro = $_COOKIE['traseiro'];
                }
            }
            ?>


            jQuery(document).ready(function($) {
                <?php
                if (has_term('configuravel', 'product_cat')) { ?>
                    <?php if ($_GET['td']) { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('<?php echo $tTraseiro ?>');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('<?php echo $tDianteiro ?>');
                        }, 500);
                    <?php } else { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('0');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('0');
                        }, 500);
                <?php }
                } ?>
            });


            <?php if (($predianteiro) || ($pretraseiro)) { ?>
                jQuery(document).ready(function($) {
                    <?php if ($predianteiro) { ?>
                        var input_dianteiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val());
                        input_dianteiro.val('<?php echo $predianteiro ?>');
                    <?php };
                    if ($pretraseiro) {
                    ?>
                        var input_traseiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val());
                        input_traseiro.val('<?php echo $pretraseiro ?>');
                    <?php }; ?>


                    firstClick = false;
                    $('.area-form-produto').css('display', 'none');
                    $('button.single_add_to_cart_button.button').css('display', 'block');
                    $('.valorescompra').css('display', 'block');

                    $('button.single_add_to_cart_button.button.alt').addClass('add_cart_fx5_pre');

                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');


                    setTimeout(function() {
                        $('.quantity input[type="button"].plus').click();
                    }, 110);
                    setTimeout(function() {
                        $('.quantity input[type="button"].minus').click();
                    }, 120);

                });
            <?php }; ?>
    <?php }; ?>
    <?php // TERMINA O IF - FX5 ?>


    <?php // INICIA O IF - FX6 ?>
    <?php if (has_term('FX6', 'product_cat')) { ?>

        jQuery('#tituloproduto').clone().appendTo(jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3'));
        jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3 #tituloproduto').addClass('mobile');

        $('button.single_add_to_cart_button.button.alt').css('padding', '12px 25px 12px 101px');
        $('button.single_add_to_cart_button.button.alt').css('font-size', '21px');

        if (jQuery("div#gform_confirmation_message_80").length) {
            firstClick = false;
            $('.area-form-produto').css('display', 'block');
            $('button.single_add_to_cart_button.button.alt').html("ADICIONAR AO CARRINHO");
            $('.single_add_to_cart_button').html('COMPRAR');
            jQuery(".above_cart_button").css('display', 'block');
        }

        $('button.single_add_to_cart_button.button.alt').html("CALCULAR VALOR");


        $('#gform_submit_button_15').click(function() {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            var valorT = jQuery('span#valor-total').html();
            var valorTra = jQuery('span#valor-traseiro').html();
            var valorDia = jQuery('span#valor-dianteiro').html();

            if (((controleT == 0) && (controleD == 0)) || (valorT == 0) || ((valorTra == 0) && (valorDia == 0))) {
                alert("Você precisa selecionar uma quantidade diferente de 0");
                return false;
            }

            // BOTÃO ENVIAR WHATSAPP
            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor')) { ?>
                jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"></div>');
                //jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"><button id="gerawhats">GERAR LINK PARA WHATSAPP</button></div>');
                //jQuery(document).on('click', '#gerawhats', function(){

                <?php
                $whatsappX = get_option('whatsapp');
                if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsappX,  $matches)) {
                    $whatsappVisualX = $matches[2] . ' ' . $matches[3] . '.' . $matches[4];
                }
                ?>
                var whatsapp1 = '<?php echo $whatsappX; ?>';
                var whatsapp2 = '<?php echo $whatsappVisualX; ?>';

                var infoDate = new Date();
                var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();

                var cliente         = jQuery("#input_80_1").val();
                var emailcliente    = jQuery("#input_80_3").val();
                var telcliente      = jQuery("#input_80_2").val();
                var veiculo         = jQuery("#input_80_6").val();
                var nomeDianteiro   = jQuery("#input_80_17").val();
                var valorDianteiro  = jQuery("#input_80_15").val();
                var nomeTraseiro    = jQuery("#input_80_18").val();
                var valorTraseiro   = jQuery("#input_80_16").val();

                var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                var tamDianteiro = 'Tamanho Original';
                var tamTraseiro = 'Tamanho Original';

                <?php if (has_term('configuravel', 'product_cat')) { ?>
                    var tamDianteiro = (jQuery('.tamanhodianteiro').find(":selected").text());
                    var tamTraseiro = (jQuery('.tamanhotraseiro').find(":selected").text());
                <?php } ?>

                var valortotal = jQuery('span#valor-total').html().replace(",", ".");
                var valortotal = Number(valortotal);
                var vardesconto = <?php echo get_option('descontoavista'); ?>;
                var desconto = '(' + vardesconto + '% de desconto)';
                var totalDesconto = (valortotal - (valortotal * (vardesconto / 100))).toFixed(2).replace(".", ",");
                var condicoes = jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '|');
                var condicoes = condicoes.replace(/(\r\n|\n|\r)/gm, "")
                var condicoes = condicoes.replace(/(<([^>]+)>)/ig, "");

                var valortotal = valortotal.toFixed(2).replace(".", ",");

                if (qtDianteiro > 0) {
                    var dianteiroProduto = 1;
                } else {
                    var dianteiroProduto = 0;
                }
                if (qtTraseiro > 0) {
                    var traseiroProduto = 1;
                } else {
                    var traseiroProduto = 0;
                }

                var long_url = '?tipo=fx5&ntipo=Amortecedor Original&tl=' + jQuery("#input_80_13").val() + '&valor2=' + valortotal + '&ti=' + veiculo + '&dP=' + dianteiroProduto + '&nD=' + nomeDianteiro + '&qD=' + qtDianteiro + '&tD=' + tamDianteiro + '&vD=' + valorDianteiro + '&tP=' + traseiroProduto + '&nT=' + nomeTraseiro + '&qT=' + qtTraseiro + '&tT=' + tamTraseiro + '&vT=' + valorTraseiro + '&d=' + myDateString + '&c=' + jQuery("#input_80_1").val() + '&mail=' + jQuery("#input_80_3").val() + '&des=' + desconto + '&todes=' + totalDesconto + '&w1=' + whatsapp1 + '&w2=' + whatsapp2 + '&im1=' + imagem1 + '&im2=' + imagem2 + '&link=<?php echo $current_url; ?>' + '&con=' + condicoes;

                console.log(long_url);

                long_url = long_url.replace(/\s/g, "%20");



                // CRIA LINK REDUZIDO
                let linkRequest = {
                    destination: 'https://offlimits.com.br/orcamento-padrao/' + long_url,
                    domain: {
                        fullName: "rebrand.ly"
                    }
                }
                let requestHeaders = {
                    "Content-Type": "application/json",
                    "apikey": "f1c72905e5764ea1bdf718eec7c26f07",
                }
                $.ajax({
                    url: "https://api.rebrandly.com/v1/links",
                    type: "post",
                    data: JSON.stringify(linkRequest),
                    headers: requestHeaders,
                    dataType: "json",
                    success: (link) => {
                        console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
                        var long_url = 'https://' + (`${link.shortUrl}`);
                        console.log('link reduzido: ' + long_url);


                        // CRIA TEXTO DO WHATSAPP

                        var txtTraseito = '';
                        var txtdianteiro = '';
                        if (qtTraseiro > '0') {
                            var txtTraseito = "• *[" + qtTraseiro + "]* " + nomeTraseiro + " - " + tamTraseiro + " | R$ " + valorTraseiro + " un. \n";
                        }
                        if (qtDianteiro > '0') {
                            var txtdianteiro = "• *[" + qtDianteiro + "]* " + nomeDianteiro + " - " + tamDianteiro + " | R$ " + valorDianteiro + " un. \n";
                        }


                        var texto = "Olá, " + cliente + "\n\n Obrigado pelo seu interesse por nossos produtos. Segue abaixo, o orçamento conforme informações fornecidas. A efetivação da sua compra pode ser feita diretamente no site ou por telefone.\n\n ------\n\n*ORÇAMENTO*\n• VEÍCULO: " + veiculo + "\n• ORÇAMENTO EMITIDO EM: " + myDateString + "\n\n" + txtTraseito + " " + txtdianteiro + "\n • *TOTAL:* R$ " + totalDesconto + ' ' + desconto + "\n\n VEJA SEU ORÇAMENTO COMPLETO: " + long_url;


                        texto = window.encodeURIComponent(texto);
                        var urlWhatsapp = 'https://web.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;
                        var urlWhatsapp2 = 'https://api.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;

                        // MONTA E EXIBE BOTÕES
                        jQuery('#Enviawhatsapp').remove();
                        jQuery('#Enviawhatsapp2').remove();
                        jQuery('.gerawhats').append('<a target="_blank" class="desktop" id="Enviawhatsapp">ENVIAR POR WHATSAPP</a>');
                        jQuery('.gerawhats').append('<a target="_blank" class="mobile" id="Enviawhatsapp2">ENVIAR POR WHATSAPP</a>');
                        $('#Enviawhatsapp').attr('href', urlWhatsapp);
                        $('#Enviawhatsapp2').attr('href', urlWhatsapp2);

                    }
                }); // END REBRAND
                //              });
            <?php } ?>
            // END WHATSAPP

        });// END $('#gform_submit_button_80').click(function()


        jQuery("ul#gform_fields_80 input").change(function() {
            jQuery('.comprawhats2').addClass('comprawhats');
            jQuery('input#input_59_1').val(jQuery('input#input_80_1').val());
            jQuery('input#input_59_3').val(jQuery('input#input_80_2').val());
            jQuery('input#input_59_2').val(jQuery('input#input_80_3').val());
        });


        $('button.single_add_to_cart_button.button').click(function(event) {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            if ((controleT != 0) || (controleD != 0)) {
                if (firstClick) {
                    event.preventDefault();

                    $('.area-form-produto').css('display', 'block');
                    $('button.single_add_to_cart_button.button').css('display', 'none');
                    $('form.cart').addClass('no-after');
                    var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                    var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                    var quantidadeDianteiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val()).slice(-2);
                    var quantidadeTraseiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val()).slice(-2);
                    var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                    var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();

                    var tot = jQuery('span#valor-total').html()
                    var des = jQuery('span#valor-desconto').html();
                    tot = tot.replace(',', '.')
                    tot = Number(tot).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    <?php global $wp;
                    $current_url = home_url(add_query_arg(array(), $wp->request)); ?>

                    //                          var Tipocalibragem = $( "select.tipocalibragem option:selected" ).val();
                    <?php if (has_term('configuravel', 'product_cat')) { ?>
                        var Tdianteiro = $("select.tamanhodianteiro option:selected").val();
                        var Ttraseiro = $("select.tamanhotraseiro option:selected").val();
                    <?php } ?>

                    var link = '<?php echo $current_url; ?>?t=' + qtTraseiro + '&d=' + qtDianteiro<?php if (has_term('configuravel', 'product_cat')) { ?> + '&tt=' + Ttraseiro + '&td=' + Tdianteiro<?php } ?>;

                    jQuery('#input_80_19').val(link);


                    //var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                    //var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                    var imagem1 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide.swiper-slide-active > div > a.jet-woo-product-gallery__image-link > img').src;

                    var imagem2 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.swiper-slide.swiper-slide-next > div > a.jet-woo-product-gallery__image-link > img').src;

                    jQuery('#input_80_20').val(imagem1);
                    jQuery('#input_80_21').val(imagem2);



                    jQuery("#input_80_24").html(
                        jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '\n')
                    );

                    var valdesconto = jQuery('.colunaprecocondicaoobs').html();
                    jQuery('#input_80_23').val(valdesconto);


                    jQuery("#input_80_27").val('<?php echo $whatsappVisual; ?>');
                    jQuery("#input_80_26").val('<?php echo $whatsapp; ?>');



                    jQuery('#input_80_8').val(quantidadeDianteiro);
                    jQuery('#input_80_9').val(quantidadeTraseiro);
                    //                          jQuery("#input_80_10").val(jQuery('select.wccpf-field[name=opes_de_calibragem] option:selected').html());
                    jQuery("#input_80_11").val(jQuery('select.wccpf-field[name=selecione_o_tipo_de_amortecedor] option:selected').html());
                    jQuery("#input_80_12").val(jQuery('#valor-total').html());
                    jQuery("#input_80_13").val(des);

                    var tras = jQuery('span#valor-traseiro').html();
                    var dia = jQuery('span#valor-dianteiro').html();

                    tras = Number(tras).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });
                    dia = Number(dia).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    var infoDate = new Date();
                    var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                    jQuery("#input_80_14").val(myDateString);
                    jQuery("#input_80_15").val(jQuery('#valor-dianteiro').html());
                    jQuery("#input_80_16").val(jQuery('#valor-traseiro').html());


                    jQuery("#input_80_25").val('<?php if (get_option('fretegratis')) {
                        echo 'GRÁTIS';
                    } else {
                        echo 'continuar a compra para calcular';
                    } ?>');

                    jQuery("#input_80_17").val((jQuery('input.titulo_produto_dianteiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhodianteiro').find(":selected").text()) <?php } ?>);
                    jQuery("#input_80_18").val((jQuery('input.titulo_produto_traseiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhotraseiro').find(":selected").text()) <?php } ?>);
                    $('button.single_add_to_cart_button.button.alt').html('ADICIONAR AO CARRINHO');

                    var position = $("#gform_fields_80").offset().top - 120;
                    $('html, body').animate({
                        scrollTop: position
                    }, 300);

                    console.log('clicou 80');
                } // Fim - if (firstClick)
                } else {
                    alert("Você precisa selecionar uma quantidade diferente de 0");
                    return false;
                }
            });

            jQuery(document).bind('gform_confirmation_loaded', function(event, formId) {
                if (formId == 80) {
                    console.log('clicou para enviar o form');
                    $('.comprawhats').css('display', 'inline-block');

                    $('.area-form-produto').css('display', 'block');
                    firstClick = false;
                    var deslocamento = $('button.single_add_to_cart_button.button').offset().top;
                    // $('html, body').animate({
                    //    scrollTop: deslocamento
                    // }, 'slow');

                    if ($("div#gform_confirmation_message_80").length) {
                        $('button.single_add_to_cart_button.button.alt').css('display', 'block');
                        $('form.cart').removeClass('no-after');
                        $('.valorescompra').css('display', 'block');
                        $('button.single_add_to_cart_button.button.alt').addClass('cart_fx5');
                        $('.single_add_to_cart_button').html('COMPRAR');
                        $(".above_cart_button").css('display', 'block');
                        //                          gtag_report_conversion();

                        var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                        var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                        var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                        var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                        $.cookie('aberto', 'sim', {
                            expires: 3
                        });
                        $.cookie('dianteiro', qtDianteiro, {
                            expires: 3
                        });
                        $.cookie('traseiro', qtTraseiro, {
                            expires: 3
                        });

                        var deslocamento = jQuery('.valorescompra.honey-size').offset().top - 120;
                        $('html, body').animate({
                            scrollTop: deslocamento
                        }, 300);

                    } // Fim - if ($("div#gform_confirmation_message_80").length)
                } // Fim - if (formId == 80)
            }); // Fim - jQuery(document).bind('gform_confirmation_loaded', function(event, formId)

            // auto preenchimento
            var cookieaberto = $.cookie('aberto');
            var cookiedianteiro = $.cookie('dianteiro');
            var cookietraseiro = $.cookie('traseiro');

            console.log(cookieaberto);
            console.log(cookiedianteiro);
            console.log(cookietraseiro);

            <?php if (has_term('configuravel', 'product_cat')) {
                $aberto = 'configuravel'; ?> console.log('configuravel');
            <?php } else { ?> console.log('nao configuravel');
            <?php } ?>

            <?php
            $aberto = $_GET['a'];
            global $woocommerce;
            $count = $woocommerce->cart->cart_contents_count;
            if ($count > 0) {
                $aberto = 'comprador'; ?> console.log('carrinho cheio');
            <?php  } else {  ?> console.log('carrinho vazio1');
            <?php } ?>

            <?php if ($aberto) { ?>

                firstClick = false;
                $('.area-form-produto').css('display', 'none');
                $('button.single_add_to_cart_button.button').css('display', 'block');
                $('button.single_add_to_cart_button.button').addClass("botinativo");
                $('button.single_add_to_cart_button.button.alt').html("SELECIONE A QUANTIDADE DE PRODUTOS");
                $(".quantity").click(function() {
                    $('button.single_add_to_cart_button.button').removeClass("botinativo");
                    $('.valorescompra').css('display', 'block');
                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');
                });

            <?php }; ?>



            <?php

            if (($_GET['t']) || ($_GET['d'])) {
                $woocommerce->cart->empty_cart();
                $predianteiro = $_GET['d'];
                $pretraseiro = $_GET['t'];
                // $calibragem = $_GET['c'];
                $tTraseiro = $_GET['tt'];
                $tDianteiro = $_GET['td'];
            ?>
                $.removeCookie('dianteiro');
                $.removeCookie('traseiro');
            <?php

            } else {
                if (isset($_COOKIE['aberto'])) {
                    $predianteiro = $_COOKIE['dianteiro'];
                    $pretraseiro = $_COOKIE['traseiro'];
                }
            }
            ?>


            jQuery(document).ready(function($) {
                <?php
                if (has_term('configuravel', 'product_cat')) { ?>
                    <?php if ($_GET['td']) { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('<?php echo $tTraseiro ?>');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('<?php echo $tDianteiro ?>');
                        }, 500);
                    <?php } else { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('0');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('0');
                        }, 500);
                <?php }
                } ?>
            });


            <?php if (($predianteiro) || ($pretraseiro)) { ?>
                jQuery(document).ready(function($) {
                    <?php if ($predianteiro) { ?>
                        var input_dianteiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val());
                        input_dianteiro.val('<?php echo $predianteiro ?>');
                    <?php };
                    if ($pretraseiro) {
                    ?>
                        var input_traseiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val());
                        input_traseiro.val('<?php echo $pretraseiro ?>');
                    <?php }; ?>


                    firstClick = false;
                    $('.area-form-produto').css('display', 'none');
                    $('button.single_add_to_cart_button.button').css('display', 'block');
                    $('.valorescompra').css('display', 'block');

                    $('button.single_add_to_cart_button.button.alt').addClass('add_cart_fx5_pre');

                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');


                    setTimeout(function() {
                        $('.quantity input[type="button"].plus').click();
                    }, 110);
                    setTimeout(function() {
                        $('.quantity input[type="button"].minus').click();
                    }, 120);

                });
            <?php }; ?>
    <?php }; ?>
    <?php // TERMINA O IF - FX6 ?>


    <?php // INICIA O IF - FX7 ?>
    <?php if (has_term('FX7', 'product_cat')) { ?>

        jQuery('#tituloproduto').clone().appendTo(jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3'));
        jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3 #tituloproduto').addClass('mobile');

        $('button.single_add_to_cart_button.button.alt').css('padding', '12px 25px 12px 101px');
        $('button.single_add_to_cart_button.button.alt').css('font-size', '21px');

        if (jQuery("div#gform_confirmation_message_77").length) {
            firstClick = false;
            $('.area-form-produto').css('display', 'block');
            $('button.single_add_to_cart_button.button.alt').html("ADICIONAR AO CARRINHO");
            $('.single_add_to_cart_button').html('COMPRAR');
            jQuery(".above_cart_button").css('display', 'block');
        }

        $('button.single_add_to_cart_button.button.alt').html("CALCULAR VALOR");


        $('#gform_submit_button_15').click(function() {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            var valorT = jQuery('span#valor-total').html();
            var valorTra = jQuery('span#valor-traseiro').html();
            var valorDia = jQuery('span#valor-dianteiro').html();

            if (((controleT == 0) && (controleD == 0)) || (valorT == 0) || ((valorTra == 0) && (valorDia == 0))) {
                alert("Você precisa selecionar uma quantidade diferente de 0");
                return false;
            }

            // BOTÃO ENVIAR WHATSAPP
            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor')) { ?>
                jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"></div>');
                //jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"><button id="gerawhats">GERAR LINK PARA WHATSAPP</button></div>');
                //jQuery(document).on('click', '#gerawhats', function(){

                <?php
                $whatsappX = get_option('whatsapp');
                if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsappX,  $matches)) {
                    $whatsappVisualX = $matches[2] . ' ' . $matches[3] . '.' . $matches[4];
                }
                ?>
                var whatsapp1 = '<?php echo $whatsappX; ?>';
                var whatsapp2 = '<?php echo $whatsappVisualX; ?>';

                var infoDate = new Date();
                var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();

                var cliente         = jQuery("#input_77_1").val();
                var emailcliente    = jQuery("#input_77_3").val();
                var telcliente      = jQuery("#input_77_2").val();
                var veiculo         = jQuery("#input_77_6").val();
                var nomeDianteiro   = jQuery("#input_77_17").val();
                var valorDianteiro  = jQuery("#input_77_15").val();
                var nomeTraseiro    = jQuery("#input_77_18").val();
                var valorTraseiro   = jQuery("#input_77_16").val();

                var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                var tamDianteiro = 'Tamanho Original';
                var tamTraseiro = 'Tamanho Original';

                <?php if (has_term('configuravel', 'product_cat')) { ?>
                    var tamDianteiro = (jQuery('.tamanhodianteiro').find(":selected").text());
                    var tamTraseiro = (jQuery('.tamanhotraseiro').find(":selected").text());
                <?php } ?>

                var valortotal = jQuery('span#valor-total').html().replace(",", ".");
                var valortotal = Number(valortotal);
                var vardesconto = <?php echo get_option('descontoavista'); ?>;
                var desconto = '(' + vardesconto + '% de desconto)';
                var totalDesconto = (valortotal - (valortotal * (vardesconto / 100))).toFixed(2).replace(".", ",");
                var condicoes = jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '|');
                var condicoes = condicoes.replace(/(\r\n|\n|\r)/gm, "")
                var condicoes = condicoes.replace(/(<([^>]+)>)/ig, "");

                var valortotal = valortotal.toFixed(2).replace(".", ",");

                if (qtDianteiro > 0) {
                    var dianteiroProduto = 1;
                } else {
                    var dianteiroProduto = 0;
                }
                if (qtTraseiro > 0) {
                    var traseiroProduto = 1;
                } else {
                    var traseiroProduto = 0;
                }

                var long_url = '?tipo=fx5&ntipo=Amortecedor Original&tl=' + jQuery("#input_77_13").val() + '&valor2=' + valortotal + '&ti=' + veiculo + '&dP=' + dianteiroProduto + '&nD=' + nomeDianteiro + '&qD=' + qtDianteiro + '&tD=' + tamDianteiro + '&vD=' + valorDianteiro + '&tP=' + traseiroProduto + '&nT=' + nomeTraseiro + '&qT=' + qtTraseiro + '&tT=' + tamTraseiro + '&vT=' + valorTraseiro + '&d=' + myDateString + '&c=' + jQuery("#input_77_1").val() + '&mail=' + jQuery("#input_77_3").val() + '&des=' + desconto + '&todes=' + totalDesconto + '&w1=' + whatsapp1 + '&w2=' + whatsapp2 + '&im1=' + imagem1 + '&im2=' + imagem2 + '&link=<?php echo $current_url; ?>' + '&con=' + condicoes;

                console.log(long_url);

                long_url = long_url.replace(/\s/g, "%20");



                // CRIA LINK REDUZIDO
                let linkRequest = {
                    destination: 'https://offlimits.com.br/orcamento-padrao/' + long_url,
                    domain: {
                        fullName: "rebrand.ly"
                    }
                }
                let requestHeaders = {
                    "Content-Type": "application/json",
                    "apikey": "f1c72905e5764ea1bdf718eec7c26f07",
                }
                $.ajax({
                    url: "https://api.rebrandly.com/v1/links",
                    type: "post",
                    data: JSON.stringify(linkRequest),
                    headers: requestHeaders,
                    dataType: "json",
                    success: (link) => {
                        console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
                        var long_url = 'https://' + (`${link.shortUrl}`);
                        console.log('link reduzido: ' + long_url);


                        // CRIA TEXTO DO WHATSAPP

                        var txtTraseito = '';
                        var txtdianteiro = '';
                        if (qtTraseiro > '0') {
                            var txtTraseito = "• *[" + qtTraseiro + "]* " + nomeTraseiro + " - " + tamTraseiro + " | R$ " + valorTraseiro + " un. \n";
                        }
                        if (qtDianteiro > '0') {
                            var txtdianteiro = "• *[" + qtDianteiro + "]* " + nomeDianteiro + " - " + tamDianteiro + " | R$ " + valorDianteiro + " un. \n";
                        }


                        var texto = "Olá, " + cliente + "\n\n Obrigado pelo seu interesse por nossos produtos. Segue abaixo, o orçamento conforme informações fornecidas. A efetivação da sua compra pode ser feita diretamente no site ou por telefone.\n\n ------\n\n*ORÇAMENTO*\n• VEÍCULO: " + veiculo + "\n• ORÇAMENTO EMITIDO EM: " + myDateString + "\n\n" + txtTraseito + " " + txtdianteiro + "\n • *TOTAL:* R$ " + totalDesconto + ' ' + desconto + "\n\n VEJA SEU ORÇAMENTO COMPLETO: " + long_url;


                        texto = window.encodeURIComponent(texto);
                        var urlWhatsapp = 'https://web.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;
                        var urlWhatsapp2 = 'https://api.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;

                        // MONTA E EXIBE BOTÕES
                        jQuery('#Enviawhatsapp').remove();
                        jQuery('#Enviawhatsapp2').remove();
                        jQuery('.gerawhats').append('<a target="_blank" class="desktop" id="Enviawhatsapp">ENVIAR POR WHATSAPP</a>');
                        jQuery('.gerawhats').append('<a target="_blank" class="mobile" id="Enviawhatsapp2">ENVIAR POR WHATSAPP</a>');
                        $('#Enviawhatsapp').attr('href', urlWhatsapp);
                        $('#Enviawhatsapp2').attr('href', urlWhatsapp2);

                    }
                }); // END REBRAND
                //              });
            <?php } ?>
            // END WHATSAPP

        });// END $('#gform_submit_button_77').click(function()


        jQuery("ul#gform_fields_77 input").change(function() {
            jQuery('.comprawhats2').addClass('comprawhats');
            jQuery('input#input_59_1').val(jQuery('input#input_77_1').val());
            jQuery('input#input_59_3').val(jQuery('input#input_77_2').val());
            jQuery('input#input_59_2').val(jQuery('input#input_77_3').val());
        });


        $('button.single_add_to_cart_button.button').click(function(event) {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            if ((controleT != 0) || (controleD != 0)) {
                if (firstClick) {
                    event.preventDefault();

                    $('.area-form-produto').css('display', 'block');
                    $('button.single_add_to_cart_button.button').css('display', 'none');
                    $('form.cart').addClass('no-after');
                    var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                    var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                    var quantidadeDianteiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val()).slice(-2);
                    var quantidadeTraseiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val()).slice(-2);
                    var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                    var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();

                    var tot = jQuery('span#valor-total').html()
                    var des = jQuery('span#valor-desconto').html();
                    tot = tot.replace(',', '.')
                    tot = Number(tot).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    <?php global $wp;
                    $current_url = home_url(add_query_arg(array(), $wp->request)); ?>

                    //                          var Tipocalibragem = $( "select.tipocalibragem option:selected" ).val();
                    <?php if (has_term('configuravel', 'product_cat')) { ?>
                        var Tdianteiro = $("select.tamanhodianteiro option:selected").val();
                        var Ttraseiro = $("select.tamanhotraseiro option:selected").val();
                    <?php } ?>

                    var link = '<?php echo $current_url; ?>?t=' + qtTraseiro + '&d=' + qtDianteiro<?php if (has_term('configuravel', 'product_cat')) { ?> + '&tt=' + Ttraseiro + '&td=' + Tdianteiro<?php } ?>;

                    jQuery('#input_77_19').val(link);


                    //var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                    //var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                    var imagem1 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide.swiper-slide-active > div > a.jet-woo-product-gallery__image-link > img').src;

                    var imagem2 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.swiper-slide.swiper-slide-next > div > a.jet-woo-product-gallery__image-link > img').src;

                    jQuery('#input_77_20').val(imagem1);
                    jQuery('#input_77_21').val(imagem2);



                    jQuery("#input_77_24").html(
                        jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '\n')
                    );

                    var valdesconto = jQuery('.colunaprecocondicaoobs').html();
                    jQuery('#input_77_23').val(valdesconto);


                    jQuery("#input_77_27").val('<?php echo $whatsappVisual; ?>');
                    jQuery("#input_77_26").val('<?php echo $whatsapp; ?>');



                    jQuery('#input_77_8').val(quantidadeDianteiro);
                    jQuery('#input_77_9').val(quantidadeTraseiro);
                    //                          jQuery("#input_77_10").val(jQuery('select.wccpf-field[name=opes_de_calibragem] option:selected').html());
                    jQuery("#input_77_11").val(jQuery('select.wccpf-field[name=selecione_o_tipo_de_amortecedor] option:selected').html());
                    jQuery("#input_77_12").val(jQuery('#valor-total').html());
                    jQuery("#input_77_13").val(des);

                    var tras = jQuery('span#valor-traseiro').html();
                    var dia = jQuery('span#valor-dianteiro').html();

                    tras = Number(tras).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });
                    dia = Number(dia).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    var infoDate = new Date();
                    var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                    jQuery("#input_77_14").val(myDateString);
                    jQuery("#input_77_15").val(jQuery('#valor-dianteiro').html());
                    jQuery("#input_77_16").val(jQuery('#valor-traseiro').html());


                    jQuery("#input_77_25").val('<?php if (get_option('fretegratis')) {
                        echo 'GRÁTIS';
                    } else {
                        echo 'continuar a compra para calcular';
                    } ?>');

                    jQuery("#input_77_17").val((jQuery('input.titulo_produto_dianteiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhodianteiro').find(":selected").text()) <?php } ?>);
                    jQuery("#input_77_18").val((jQuery('input.titulo_produto_traseiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhotraseiro').find(":selected").text()) <?php } ?>);
                    $('button.single_add_to_cart_button.button.alt').html('ADICIONAR AO CARRINHO');

                    var position = $("#gform_fields_77").offset().top - 120;
                    $('html, body').animate({
                        scrollTop: position
                    }, 300);

                    console.log('clicou 77');
                } // Fim - if (firstClick)
                } else {
                    alert("Você precisa selecionar uma quantidade diferente de 0");
                    return false;
                }
            });

            jQuery(document).bind('gform_confirmation_loaded', function(event, formId) {
                if (formId == 77) {
                    console.log('clicou para enviar o form');
                    $('.comprawhats').css('display', 'inline-block');

                    $('.area-form-produto').css('display', 'block');
                    firstClick = false;
                    var deslocamento = $('button.single_add_to_cart_button.button').offset().top;
                    // $('html, body').animate({
                    //    scrollTop: deslocamento
                    // }, 'slow');

                    if ($("div#gform_confirmation_message_77").length) {
                        $('button.single_add_to_cart_button.button.alt').css('display', 'block');
                        $('form.cart').removeClass('no-after');
                        $('.valorescompra').css('display', 'block');
                        $('button.single_add_to_cart_button.button.alt').addClass('cart_fx5');
                        $('.single_add_to_cart_button').html('COMPRAR');
                        $(".above_cart_button").css('display', 'block');
                        //                          gtag_report_conversion();

                        var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                        var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                        var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                        var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                        $.cookie('aberto', 'sim', {
                            expires: 3
                        });
                        $.cookie('dianteiro', qtDianteiro, {
                            expires: 3
                        });
                        $.cookie('traseiro', qtTraseiro, {
                            expires: 3
                        });

                        var deslocamento = jQuery('.valorescompra.honey-size').offset().top - 120;
                        $('html, body').animate({
                            scrollTop: deslocamento
                        }, 300);

                    } // Fim - if ($("div#gform_confirmation_message_77").length)
                } // Fim - if (formId == 77)
            }); // Fim - jQuery(document).bind('gform_confirmation_loaded', function(event, formId)

            // auto preenchimento
            var cookieaberto = $.cookie('aberto');
            var cookiedianteiro = $.cookie('dianteiro');
            var cookietraseiro = $.cookie('traseiro');

            console.log(cookieaberto);
            console.log(cookiedianteiro);
            console.log(cookietraseiro);

            <?php if (has_term('configuravel', 'product_cat')) {
                $aberto = 'configuravel'; ?> console.log('configuravel');
            <?php } else { ?> console.log('nao configuravel');
            <?php } ?>

            <?php
            $aberto = $_GET['a'];
            global $woocommerce;
            $count = $woocommerce->cart->cart_contents_count;
            if ($count > 0) {
                $aberto = 'comprador'; ?> console.log('carrinho cheio');
            <?php  } else {  ?> console.log('carrinho vazio1');
            <?php } ?>

            <?php if ($aberto) { ?>

                firstClick = false;
                $('.area-form-produto').css('display', 'none');
                $('button.single_add_to_cart_button.button').css('display', 'block');
                $('button.single_add_to_cart_button.button').addClass("botinativo");
                $('button.single_add_to_cart_button.button.alt').html("SELECIONE A QUANTIDADE DE PRODUTOS");
                $(".quantity").click(function() {
                    $('button.single_add_to_cart_button.button').removeClass("botinativo");
                    $('.valorescompra').css('display', 'block');
                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');
                });

            <?php }; ?>



            <?php

            if (($_GET['t']) || ($_GET['d'])) {
                $woocommerce->cart->empty_cart();
                $predianteiro = $_GET['d'];
                $pretraseiro = $_GET['t'];
                // $calibragem = $_GET['c'];
                $tTraseiro = $_GET['tt'];
                $tDianteiro = $_GET['td'];
            ?>
                $.removeCookie('dianteiro');
                $.removeCookie('traseiro');
            <?php

            } else {
                if (isset($_COOKIE['aberto'])) {
                    $predianteiro = $_COOKIE['dianteiro'];
                    $pretraseiro = $_COOKIE['traseiro'];
                }
            }
            ?>


            jQuery(document).ready(function($) {
                <?php
                if (has_term('configuravel', 'product_cat')) { ?>
                    <?php if ($_GET['td']) { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('<?php echo $tTraseiro ?>');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('<?php echo $tDianteiro ?>');
                        }, 500);
                    <?php } else { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('0');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('0');
                        }, 500);
                <?php }
                } ?>
            });


            <?php if (($predianteiro) || ($pretraseiro)) { ?>
                jQuery(document).ready(function($) {
                    <?php if ($predianteiro) { ?>
                        var input_dianteiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val());
                        input_dianteiro.val('<?php echo $predianteiro ?>');
                    <?php };
                    if ($pretraseiro) {
                    ?>
                        var input_traseiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val());
                        input_traseiro.val('<?php echo $pretraseiro ?>');
                    <?php }; ?>


                    firstClick = false;
                    $('.area-form-produto').css('display', 'none');
                    $('button.single_add_to_cart_button.button').css('display', 'block');
                    $('.valorescompra').css('display', 'block');

                    $('button.single_add_to_cart_button.button.alt').addClass('add_cart_fx5_pre');

                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');


                    setTimeout(function() {
                        $('.quantity input[type="button"].plus').click();
                    }, 110);
                    setTimeout(function() {
                        $('.quantity input[type="button"].minus').click();
                    }, 120);

                });
            <?php }; ?>
    <?php }; ?>
    <?php // TERMINA O IF - FX7 ?>


    <?php // INICIA O IF - FX8 ?>
    <?php if (has_term('FX8', 'product_cat')) { ?>

        jQuery('#tituloproduto').clone().appendTo(jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3'));
        jQuery('div#av_section_1 .av-special-heading.av-special-heading-h3 #tituloproduto').addClass('mobile');

        $('button.single_add_to_cart_button.button.alt').css('padding', '12px 25px 12px 101px');
        $('button.single_add_to_cart_button.button.alt').css('font-size', '21px');

        if (jQuery("div#gform_confirmation_message_83").length) {
            firstClick = false;
            $('.area-form-produto').css('display', 'block');
            $('button.single_add_to_cart_button.button.alt').html("ADICIONAR AO CARRINHO");
            $('.single_add_to_cart_button').html('COMPRAR');
            jQuery(".above_cart_button").css('display', 'block');
        }

        $('button.single_add_to_cart_button.button.alt').html("CALCULAR VALOR");


        $('#gform_submit_button_83').click(function() {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            var valorT = jQuery('span#valor-total').html();
            var valorTra = jQuery('span#valor-traseiro').html();
            var valorDia = jQuery('span#valor-dianteiro').html();

            if (((controleT == 0) && (controleD == 0)) || (valorT == 0) || ((valorTra == 0) && (valorDia == 0))) {
                alert("Você precisa selecionar uma quantidade diferente de 0");
                return false;
            }

            // BOTÃO ENVIAR WHATSAPP
            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor')) { ?>
                jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"></div>');
                //jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"><button id="gerawhats">GERAR LINK PARA WHATSAPP</button></div>');
                //jQuery(document).on('click', '#gerawhats', function(){

                <?php
                $whatsappX = get_option('whatsapp');
                if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsappX,  $matches)) {
                    $whatsappVisualX = $matches[2] . ' ' . $matches[3] . '.' . $matches[4];
                }
                ?>
                var whatsapp1 = '<?php echo $whatsappX; ?>';
                var whatsapp2 = '<?php echo $whatsappVisualX; ?>';

                var infoDate = new Date();
                var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();

                var cliente         = jQuery("#input_83_1").val();
                var emailcliente    = jQuery("#input_83_3").val();
                var telcliente      = jQuery("#input_83_2").val();
                var veiculo         = jQuery("#input_83_6").val();
                var nomeDianteiro   = jQuery("#input_83_17").val();
                var valorDianteiro  = jQuery("#input_83_15").val();
                var nomeTraseiro    = jQuery("#input_83_18").val();
                var valorTraseiro   = jQuery("#input_83_16").val();

                var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                var tamDianteiro = 'Tamanho Original';
                var tamTraseiro = 'Tamanho Original';

                <?php if (has_term('configuravel', 'product_cat')) { ?>
                    var tamDianteiro = (jQuery('.tamanhodianteiro').find(":selected").text());
                    var tamTraseiro = (jQuery('.tamanhotraseiro').find(":selected").text());
                <?php } ?>

                var valortotal = jQuery('span#valor-total').html().replace(",", ".");
                var valortotal = Number(valortotal);
                var vardesconto = <?php echo get_option('descontoavista'); ?>;
                var desconto = '(' + vardesconto + '% de desconto)';
                var totalDesconto = (valortotal - (valortotal * (vardesconto / 100))).toFixed(2).replace(".", ",");
                var condicoes = jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '|');
                var condicoes = condicoes.replace(/(\r\n|\n|\r)/gm, "")
                var condicoes = condicoes.replace(/(<([^>]+)>)/ig, "");

                var valortotal = valortotal.toFixed(2).replace(".", ",");

                if (qtDianteiro > 0) {
                    var dianteiroProduto = 1;
                } else {
                    var dianteiroProduto = 0;
                }
                if (qtTraseiro > 0) {
                    var traseiroProduto = 1;
                } else {
                    var traseiroProduto = 0;
                }

                var long_url = '?tipo=fx8&ntipo=Amortecedor Especial&tl=' + jQuery("#input_83_13").val() + '&valor2=' + valortotal + '&ti=' + veiculo + '&dP=' + dianteiroProduto + '&nD=' + nomeDianteiro + '&qD=' + qtDianteiro + '&tD=' + tamDianteiro + '&vD=' + valorDianteiro + '&tP=' + traseiroProduto + '&nT=' + nomeTraseiro + '&qT=' + qtTraseiro + '&tT=' + tamTraseiro + '&vT=' + valorTraseiro + '&d=' + myDateString + '&c=' + jQuery("#input_83_1").val() + '&mail=' + jQuery("#input_83_3").val() + '&des=' + desconto + '&todes=' + totalDesconto + '&w1=' + whatsapp1 + '&w2=' + whatsapp2 + '&im1=' + imagem1 + '&im2=' + imagem2 + '&link=<?php echo $current_url; ?>' + '&con=' + condicoes;

                console.log(long_url);

                long_url = long_url.replace(/\s/g, "%20");



                // CRIA LINK REDUZIDO
                let linkRequest = {
                    destination: 'https://offlimits.com.br/orcamento-padrao/' + long_url,
                    domain: {
                        fullName: "rebrand.ly"
                    }
                }
                let requestHeaders = {
                    "Content-Type": "application/json",
                    "apikey": "f1c72905e5764ea1bdf718eec7c26f07",
                }
                $.ajax({
                    url: "https://api.rebrandly.com/v1/links",
                    type: "post",
                    data: JSON.stringify(linkRequest),
                    headers: requestHeaders,
                    dataType: "json",
                    success: (link) => {
                        console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
                        var long_url = 'https://' + (`${link.shortUrl}`);
                        console.log('link reduzido: ' + long_url);


                        // CRIA TEXTO DO WHATSAPP

                        var txtTraseito = '';
                        var txtdianteiro = '';
                        if (qtTraseiro > '0') {
                            var txtTraseito = "• *[" + qtTraseiro + "]* " + nomeTraseiro + " - " + tamTraseiro + " | R$ " + valorTraseiro + " un. \n";
                        }
                        if (qtDianteiro > '0') {
                            var txtdianteiro = "• *[" + qtDianteiro + "]* " + nomeDianteiro + " - " + tamDianteiro + " | R$ " + valorDianteiro + " un. \n";
                        }


                        var texto = "Olá, " + cliente + "\n\n Obrigado pelo seu interesse por nossos produtos. Segue abaixo, o orçamento conforme informações fornecidas. A efetivação da sua compra pode ser feita diretamente no site ou por telefone.\n\n ------\n\n*ORÇAMENTO*\n• VEÍCULO: " + veiculo + "\n• ORÇAMENTO EMITIDO EM: " + myDateString + "\n\n" + txtTraseito + " " + txtdianteiro + "\n • *TOTAL:* R$ " + totalDesconto + ' ' + desconto + "\n\n VEJA SEU ORÇAMENTO COMPLETO: " + long_url;


                        texto = window.encodeURIComponent(texto);
                        var urlWhatsapp = 'https://web.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;
                        var urlWhatsapp2 = 'https://api.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;

                        // MONTA E EXIBE BOTÕES
                        jQuery('#Enviawhatsapp').remove();
                        jQuery('#Enviawhatsapp2').remove();
                        jQuery('.gerawhats').append('<a target="_blank" class="desktop" id="Enviawhatsapp">ENVIAR POR WHATSAPP</a>');
                        jQuery('.gerawhats').append('<a target="_blank" class="mobile" id="Enviawhatsapp2">ENVIAR POR WHATSAPP</a>');
                        $('#Enviawhatsapp').attr('href', urlWhatsapp);
                        $('#Enviawhatsapp2').attr('href', urlWhatsapp2);

                    }
                }); // END REBRAND
                //              });
            <?php } ?>
            // END WHATSAPP

        });// END $('#gform_submit_button_83').click(function()


        jQuery("ul#gform_fields_83 input").change(function() {
            jQuery('.comprawhats2').addClass('comprawhats');
            jQuery('input#input_59_1').val(jQuery('input#input_83_1').val());
            jQuery('input#input_59_3').val(jQuery('input#input_83_2').val());
            jQuery('input#input_59_2').val(jQuery('input#input_83_3').val());
        });


        $('button.single_add_to_cart_button.button').click(function(event) {

            var controleT = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val()).val()).slice(-2);
            var controleD = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val()).val()).slice(-2);
            if ((controleT != 0) || (controleD != 0)) {
                if (firstClick) {
                    event.preventDefault();

                    $('.area-form-produto').css('display', 'block');
                    $('button.single_add_to_cart_button.button').css('display', 'none');
                    $('form.cart').addClass('no-after');
                    var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                    var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                    var quantidadeDianteiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val()).slice(-2);
                    var quantidadeTraseiro = ('0' + jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val()).slice(-2);
                    var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                    var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();

                    var tot = jQuery('span#valor-total').html()
                    var des = jQuery('span#valor-desconto').html();
                    tot = tot.replace(',', '.')
                    tot = Number(tot).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    <?php global $wp;
                    $current_url = home_url(add_query_arg(array(), $wp->request)); ?>

                    //                          var Tipocalibragem = $( "select.tipocalibragem option:selected" ).val();
                    <?php if (has_term('configuravel', 'product_cat')) { ?>
                        var Tdianteiro = $("select.tamanhodianteiro option:selected").val();
                        var Ttraseiro = $("select.tamanhotraseiro option:selected").val();
                    <?php } ?>

                    var link = '<?php echo $current_url; ?>?t=' + qtTraseiro + '&d=' + qtDianteiro<?php if (has_term('configuravel', 'product_cat')) { ?> + '&tt=' + Ttraseiro + '&td=' + Tdianteiro<?php } ?>;

                    jQuery('#input_83_19').val(link);


                    //var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                    //var imagem2 = jQuery('.avia-gallery-thumb a:eq( 1 )').attr('data-prev-img');
                    var imagem1 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide.swiper-slide-active > div > a.jet-woo-product-gallery__image-link > img').src;

                    var imagem2 = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.swiper-slide.swiper-slide-next > div > a.jet-woo-product-gallery__image-link > img').src;

                    jQuery('#input_83_20').val(imagem1);
                    jQuery('#input_83_21').val(imagem2);



                    jQuery("#input_83_24").html(
                        jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '\n')
                    );

                    var valdesconto = jQuery('.colunaprecocondicaoobs').html();
                    jQuery('#input_83_23').val(valdesconto);


                    jQuery("#input_83_27").val('<?php echo $whatsappVisual; ?>');
                    jQuery("#input_83_26").val('<?php echo $whatsapp; ?>');



                    jQuery('#input_83_8').val(quantidadeDianteiro);
                    jQuery('#input_83_9').val(quantidadeTraseiro);
                    //                          jQuery("#input_83_10").val(jQuery('select.wccpf-field[name=opes_de_calibragem] option:selected').html());
                    jQuery("#input_83_11").val(jQuery('select.wccpf-field[name=selecione_o_tipo_de_amortecedor] option:selected').html());
                    jQuery("#input_83_12").val(jQuery('#valor-total').html());
                    jQuery("#input_83_13").val(des);

                    var tras = jQuery('span#valor-traseiro').html();
                    var dia = jQuery('span#valor-dianteiro').html();

                    tras = Number(tras).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });
                    dia = Number(dia).toLocaleString('pt-br', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    var infoDate = new Date();
                    var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                    jQuery("#input_83_14").val(myDateString);
                    jQuery("#input_83_15").val(jQuery('#valor-dianteiro').html());
                    jQuery("#input_83_16").val(jQuery('#valor-traseiro').html());


                    jQuery("#input_83_25").val('<?php if (get_option('fretegratis')) {
                        echo 'GRÁTIS';
                    } else {
                        echo 'continuar a compra para calcular';
                    } ?>');

                    jQuery("#input_83_17").val((jQuery('input.titulo_produto_dianteiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhodianteiro').find(":selected").text()) <?php } ?>);
                    jQuery("#input_83_18").val((jQuery('input.titulo_produto_traseiro').val()) <?php if (has_term('configuravel', 'product_cat')) { ?> + " " + (jQuery('.tamanhotraseiro').find(":selected").text()) <?php } ?>);
                    $('button.single_add_to_cart_button.button.alt').html('ADICIONAR AO CARRINHO');

                    var position = $("#gform_fields_83").offset().top - 120;
                    $('html, body').animate({
                        scrollTop: position
                    }, 300);

                    console.log('clicou 83');
                } // Fim - if (firstClick)
                } else {
                    alert("Você precisa selecionar uma quantidade diferente de 0");
                    return false;
                }
            });

            jQuery(document).bind('gform_confirmation_loaded', function(event, formId) {
                if (formId == 83) {
                    console.log('clicou para enviar o form');
                    $('.comprawhats').css('display', 'inline-block');

                    $('.area-form-produto').css('display', 'block');
                    firstClick = false;
                    var deslocamento = $('button.single_add_to_cart_button.button').offset().top;
                    // $('html, body').animate({
                    //    scrollTop: deslocamento
                    // }, 'slow');

                    if ($("div#gform_confirmation_message_83").length) {
                        $('button.single_add_to_cart_button.button.alt').css('display', 'block');
                        $('form.cart').removeClass('no-after');
                        $('.valorescompra').css('display', 'block');
                        $('button.single_add_to_cart_button.button.alt').addClass('cart_fx5');
                        $('.single_add_to_cart_button').html('COMPRAR');
                        $(".above_cart_button").css('display', 'block');
                        //                          gtag_report_conversion();

                        var idProdutoTraseiro = jQuery('#id_product_traseiro').val();
                        var idProdutoDianteiro = jQuery('#id_product_dianteiro').val();
                        var qtDianteiro = jQuery('input.input-text.qty.text.qty-' + idProdutoDianteiro).val();
                        var qtTraseiro = jQuery('input.input-text.qty.text.qty-' + idProdutoTraseiro).val();
                        $.cookie('aberto', 'sim', {
                            expires: 3
                        });
                        $.cookie('dianteiro', qtDianteiro, {
                            expires: 3
                        });
                        $.cookie('traseiro', qtTraseiro, {
                            expires: 3
                        });

                        var deslocamento = jQuery('.valorescompra.honey-size').offset().top - 120;
                        $('html, body').animate({
                            scrollTop: deslocamento
                        }, 300);

                    } // Fim - if ($("div#gform_confirmation_message_83").length)
                } // Fim - if (formId == 83)
            }); // Fim - jQuery(document).bind('gform_confirmation_loaded', function(event, formId)

            // auto preenchimento
            var cookieaberto = $.cookie('aberto');
            var cookiedianteiro = $.cookie('dianteiro');
            var cookietraseiro = $.cookie('traseiro');

            console.log(cookieaberto);
            console.log(cookiedianteiro);
            console.log(cookietraseiro);

            <?php if (has_term('configuravel', 'product_cat')) {
                $aberto = 'configuravel'; ?> console.log('configuravel');
            <?php } else { ?> console.log('nao configuravel');
            <?php } ?>

            <?php
            $aberto = $_GET['a'];
            global $woocommerce;
            $count = $woocommerce->cart->cart_contents_count;
            if ($count > 0) {
                $aberto = 'comprador'; ?> console.log('carrinho cheio');
            <?php  } else {  ?> console.log('carrinho vazio1');
            <?php } ?>

            <?php if ($aberto) { ?>

                firstClick = false;
                $('.area-form-produto').css('display', 'none');
                $('button.single_add_to_cart_button.button').css('display', 'block');
                $('button.single_add_to_cart_button.button').addClass("botinativo");
                $('button.single_add_to_cart_button.button.alt').html("SELECIONE A QUANTIDADE DE PRODUTOS");
                $(".quantity").click(function() {
                    $('button.single_add_to_cart_button.button').removeClass("botinativo");
                    $('.valorescompra').css('display', 'block');
                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');
                });

            <?php }; ?>



            <?php

            if (($_GET['t']) || ($_GET['d'])) {
                $woocommerce->cart->empty_cart();
                $predianteiro = $_GET['d'];
                $pretraseiro = $_GET['t'];
                // $calibragem = $_GET['c'];
                $tTraseiro = $_GET['tt'];
                $tDianteiro = $_GET['td'];
            ?>
                $.removeCookie('dianteiro');
                $.removeCookie('traseiro');
            <?php

            } else {
                if (isset($_COOKIE['aberto'])) {
                    $predianteiro = $_COOKIE['dianteiro'];
                    $pretraseiro = $_COOKIE['traseiro'];
                }
            }
            ?>


            jQuery(document).ready(function($) {
                <?php
                if (has_term('configuravel', 'product_cat')) { ?>
                    <?php if ($_GET['td']) { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('<?php echo $tTraseiro ?>');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('<?php echo $tDianteiro ?>');
                        }, 500);
                    <?php } else { ?>
                        setTimeout(function() {
                            $("select.tamanhodianteiro").val('0');
                        }, 500);
                        setTimeout(function() {
                            $("select.tamanhotraseiro").val('0');
                        }, 500);
                <?php }
                } ?>
            });


            <?php if (($predianteiro) || ($pretraseiro)) { ?>
                jQuery(document).ready(function($) {
                    <?php if ($predianteiro) { ?>
                        var input_dianteiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_dianteiro').val());
                        input_dianteiro.val('<?php echo $predianteiro ?>');
                    <?php };
                    if ($pretraseiro) {
                    ?>
                        var input_traseiro = jQuery('input.input-text.qty.text.qty-' + jQuery('#id_product_traseiro').val());
                        input_traseiro.val('<?php echo $pretraseiro ?>');
                    <?php }; ?>


                    firstClick = false;
                    $('.area-form-produto').css('display', 'none');
                    $('button.single_add_to_cart_button.button').css('display', 'block');
                    $('.valorescompra').css('display', 'block');

                    $('button.single_add_to_cart_button.button.alt').addClass('add_cart_fx8_pre');

                    $('.single_add_to_cart_button').html('COMPRAR');
                    $(".above_cart_button").css('display', 'none');


                    setTimeout(function() {
                        $('.quantity input[type="button"].plus').click();
                    }, 110);
                    setTimeout(function() {
                        $('.quantity input[type="button"].minus').click();
                    }, 120);

                });
            <?php }; ?>
    <?php }; ?>
    <?php // TERMINA O IF - FX8 ?>


    <?php //INICIA O IF - FXM ?>
    <?php echo get_term('product_cat'); ?>
    <?php if (has_term(['FXM'], 'product_cat')) { ?>

                    <?php $btnKit = get_post_meta(get_the_ID(), 'botao-kitlift', true);

                    if ($btnKit == "orcamento") { ?>

                        $('button.single_add_to_cart_button.button.alt').css('padding', '12px 25px 12px 101px');
                        $('button.single_add_to_cart_button.button.alt').css('font-size', '21px');

                        if (jQuery("div#gform_confirmation_message_72").length) {
                            firstClick = false;
                            $('.area-form-produto').css('display', 'block');
                            $('button.single_add_to_cart_button.button.alt').html("ADICIONAR AO CARRINHO");
                            $('.valorescompra').css('display', 'block');
                            $('.single_add_to_cart_button').html('COMPRAR');
                            jQuery(".above_cart_button").css('display', 'block');
                        }

                        $('button.single_add_to_cart_button.button.alt').html("CALCULAR VALOR");

                        $('#gform_submit_button_72').click(function() {
                            var controleQua = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_produto').html()).val()).slice(-2);
                            var controleVal = jQuery('span#valor-total').html();
                            if (controleQua == 0 || controleVal == 0) {
                                alert("Você precisa selecionar uma quantidade diferente de 0");
                                return false;
                            }


                            // cria botão whatsapp
                            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor')) { ?>
                                jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"></div>');
                                //              jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"><button id="gerawhats">GERAR LINK PARA WHATSAPP</button></div>');
                                //              jQuery(document).on('click', '#gerawhats', function(){
                                var infoDate = new Date();
                                var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                                var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                                var cliente = jQuery("#input_72_1").val();
                                var emailcliente = jQuery("#input_72_3").val();
                                var telcliente = jQuery("#input_72_2").val();
                                var veiculo = jQuery("#input_72_20").val();
                                var quantidade = jQuery("#input_72_19").val();
                                var valortotal = jQuery('span#valor-total').html().replace(",", ".");
                                var valortotal = Number(valortotal);
                                var vardesconto = <?php echo get_option('descontoavista'); ?>;
                                var desconto = '(' + vardesconto + '% de desconto)';
                                var totalDesconto = (valortotal - (valortotal * (vardesconto / 100))).toFixed(2).replace(".", ",");
                                var condicoes = jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '|');
                                var condicoes = condicoes.replace(/(\r\n|\n|\r)/gm, "")
                                var condicoes = condicoes.replace(/(<([^>]+)>)/ig, "");

                                var valortotal = valortotal.toFixed(2).replace(".", ",");

                                <?php
                                $whatsappX = get_option('whatsapp');
                                if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsappX,  $matches)) {
                                    $whatsappVisualX = $matches[2] . ' ' . $matches[3] . '.' . $matches[4];
                                }
                                ?>
                                var whatsapp1 = '<?php echo $whatsappX; ?>';
                                var whatsapp2 = '<?php echo $whatsappVisualX; ?>';


                                // CRIA LINK DO ORÇAMENTO
                                var long_url = '?tipo=fxm&ntipo=fxm&tl=' + jQuery("#input_72_13").val() + '&valor2=' + valortotal + '&ti=' + jQuery("#input_72_20").val() + '&d=' + myDateString + '&c=' + jQuery("#input_72_1").val() + '&mail=' + jQuery("#input_72_3").val() + '&des=' + desconto + '&todes=' + totalDesconto + '&w1=' + whatsapp1 + '&w2=' + whatsapp2 + '&foto2=' + imagem1 + '&qU=' + quantidade + '&link=<?php echo $current_url; ?>' + '&con=' + condicoes;

                                long_url = long_url.replace(/\s/g, "%20");


                                // CRIA LINK REDUZIDO
                                let linkRequest = {
                                    destination: 'https://offlimits.com.br/orcamento-padrao/' + long_url,
                                    domain: {
                                        fullName: "rebrand.ly"
                                    }
                                }
                                let requestHeaders = {
                                    "Content-Type": "application/json",
                                    "apikey": "f1c72905e5764ea1bdf718eec7c26f07",
                                }
                                $.ajax({
                                    url: "https://api.rebrandly.com/v1/links",
                                    type: "post",
                                    data: JSON.stringify(linkRequest),
                                    headers: requestHeaders,
                                    dataType: "json",
                                    success: (link) => {
                                        console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
                                        var long_url = 'https://' + (`${link.shortUrl}`);
                                        console.log('link reduzido: ' + long_url);


                                        // CRIA TEXTO DO WHATSAPP

                                        var texto = 'Olá, ' + cliente + '\n\n Obrigado pelo seu interesse por nossos produtos. Segue abaixo, o orçamento conforme informações fornecidas. A efetivação da sua compra pode ser feita diretamente no site ou por telefone.\n\n ------\n\n*ORÇAMENTO*\n• VEÍCULO: ' + veiculo + '\n• ORÇAMENTO EMITIDO EM: ' + myDateString + '\n\n • *[ ' + quantidade + ' ]* FXM | R$ ' + valortotal + ' \n\n • *TOTAL:* R$ ' + totalDesconto + ' ' + desconto + '\n\n VEJA SEU ORÇAMENTO COMPLETO: ' + long_url;

                                        texto = window.encodeURIComponent(texto);
                                        var urlWhatsapp = 'https://web.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;
                                        var urlWhatsapp2 = 'https://api.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;

                                        // MONTA E EXIBE BOTÕES
                                        jQuery('#Enviawhatsapp').remove();
                                        jQuery('#Enviawhatsapp2').remove();
                                        jQuery('.gerawhats').append('<a target="_blank" class="desktop" id="Enviawhatsapp">ENVIAR POR WHATSAPP</a>');
                                        jQuery('.gerawhats').append('<a target="_blank" class="mobile" id="Enviawhatsapp2">ENVIAR POR WHATSAPP</a>');
                                        $('#Enviawhatsapp').attr('href', urlWhatsapp);
                                        $('#Enviawhatsapp2').attr('href', urlWhatsapp2);

                                    }
                                }); // END REBRAND
                                //          });
                            <?php } ?>
                            // end whatsapp
                        })


                        jQuery("ul#gform_fields_72 input").change(function() {
                            jQuery('.comprawhats2').addClass('comprawhats');
                            jQuery('input#input_59_1').val(jQuery('input#input_72_1').val());
                            jQuery('input#input_59_3').val(jQuery('input#input_72_2').val());
                            jQuery('input#input_59_2').val(jQuery('input#input_72_3').val());
                        });


                        $('button.single_add_to_cart_button.button').click(function() {

                            console.log("felipe entrou");

                            var controle = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_produto').html()).val()).slice(-2);
                            if (controle != 0) {

                                if (firstClick) {
                                    event.preventDefault();
                                    $('.area-form-produto').css('display', 'block');
                                    $('button.single_add_to_cart_button.button').css('display', 'none');
                                    $('form.cart').addClass('no-after');
                                    var idProduto = jQuery('#id_produto').html();
                                    var quantProduto = ('0' + jQuery('input.input-text.qty.text.qty-' + idProduto).val()).slice(-2);
                                    //                          jQuery("#input_19_10").val(jQuery('select.wccpf-field[name=opes_de_calibragem_] option:selected').html());
                                    jQuery("#input_72_12").val(jQuery('span#valor-total').html());
                                    jQuery("#input_72_13").val(jQuery('span#valor-desconto').html());


                                    jQuery("#input_72_28").val('<?php echo $whatsappVisual; ?>');
                                    jQuery("#input_72_27").val('<?php echo $whatsapp; ?>');


                                    jQuery("#input_72_26").val('<?php if (get_option('fretegratis')) {
                                                                    echo 'GRÁTIS';
                                                                } else {
                                                                    echo 'continuar a compra para calcular';
                                                                } ?>');


                                    <?php //criação da data
                                    ?>
                                    var infoDate = new Date();
                                    var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                                    jQuery("#input_72_14").val(myDateString);
                                    jQuery("#input_72_18").val(jQuery('h1.av-special-heading-tag').html());
                                    jQuery("#input_72_19").val(quantProduto);
                                    <?php //pegando veiculo
                                    ?>
                                    jQuery("#input_72_20").val(jQuery('.av-woo-purchase-button h3.av-special-heading-tag').html());



                                    var qtProduto = jQuery('input.input-text.qty.text.qty-' + idProduto).val();

                                    <?php
                                    global $wp;
                                    $current_url = home_url(add_query_arg(array(), $wp->request));
                                    ?>

                                    //                          var calibragemval = jQuery('select.wccpf-field[name=opes_de_calibragem] option:selected').val();

                                    var link = '<?php echo $current_url; ?>?q=' + qtProduto;
                                    jQuery('#input_72_21').val(link);

                                    var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                                    var sourceThumbnail = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide.swiper-slide-active > div > a.jet-woo-product-gallery__image-link > img')

                                    if(!sourceThumbnail)
                                        sourceThumbnail = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide > div.jet-woo-product-gallery__image > a.jet-woo-product-gallery__image-link > img')

                                    if(sourceThumbnail){
                                        imagem1 = sourceThumbnail.src
                                    }

                                    console.log(imagem1)
                                    $("#input_72_38").val(imagem1);

                                    jQuery("#input_72_25").html(jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '\n'));

                                    var valdesconto = jQuery('.colunaprecocondicaoobs').html();
                                    jQuery('#input_72_24').val(valdesconto);

                                    $('button.single_add_to_cart_button.button.alt').html('ADICIONAR AO CARRINHO');

                                    var position = $("#gform_fields_72").offset().top - 120;
                                    // $('html, body').animate({
                                    //     scrollTop: position
                                    // }, 300);
                                    // console.log('clicou 71');

                                } // FIM - if (firstClick)

                            } else {
                                alert("Você precisa selecionar uma quantidade maior que 0");
                                return false;
                            }
                        }); // $('button.single_add_to_cart_button.button').click(function()


                        jQuery(document).bind('gform_confirmation_loaded', function(event, formId) {

                            if (formId == 72) {
                                console.log('clicou para enviar o form');



                                $('.area-form-produto').css('display', 'block');
                                $('.comprawhats').css('display', 'inline-block');
                                firstClick = false;
                                var deslocamento = jQuery('button.single_add_to_cart_button.button').offset().top - 120;
                                // $('html, body').animate({
                                //     scrollTop: deslocamento
                                // }, 'slow');

                                if (jQuery("div#gform_confirmation_message_72").length) {
                                    $('button.single_add_to_cart_button.button.alt').css('display', 'block');
                                    $('form.cart').removeClass('no-after');
                                    $('.valorescompra').css('display', 'block');

                                    //verificar
                                    $('button.single_add_to_cart_button.button.alt').addClass('cart_kit');
                                    //                          gtag_report_conversion();

                                    $('.single_add_to_cart_button').html('COMPRAR');
                                    jQuery(".above_cart_button").css('display', 'block');

                                    var qtyProduto = jQuery('input.input-text.qty.text.qty').val();
                                    $.cookie('aberto', 'sim', {
                                        expires: 3
                                    });
                                    $.cookie('quantidade', qtyProduto, {
                                        expires: 3
                                    });


                                    var deslocamento = jQuery('.valorescompra.honey-size').offset().top - 120;
                                    // $('html, body').animate({
                                    //     scrollTop: deslocamento
                                    // }, 300);

                                }

                            } // Fim - if (formId == 72)

                        }); // Fim - jQuery(document).bind('gform_confirmation_loaded

                    <?php } ?> // FIM - if ($btnKit == "orcamento")


                    // auto preencimento
                    // var cookieaberto = $.cookie('aberto');
                    var cookieaberto = false;
                    var cookieqty = $.cookie('quantidade');

                    console.log(cookieaberto);
                    console.log(cookieqty);



                    <?php
                    $aberto = $_GET['a'];
                    global $woocommerce;
                    $count = $woocommerce->cart->cart_contents_count;
                    if ($count > 0) {
                        $aberto = 'comprador'; ?> console.log('carrinho cheio');
                    <?php  } else {  ?> console.log('carrinho vazio');
                    <?php }    ?>

                    <?php // if(has_term( 'Configuravel', 'product_cat' )){ $aberto = 'configuravel'; console.log('carrinho cheio'); } else { console.log('nao configuravel'); }?>

                    <?php if (($abertoo)) { ?>

                        firstClick = false;
                        $('.comprawhats').css('display', 'inline-block');
                        $('.area-form-produto').css('display', 'none');
                        $('button.single_add_to_cart_button.button').css('display', 'block');
                        $('button.single_add_to_cart_button.button').addClass("botinativo");
                        $('button.single_add_to_cart_button.button.alt').html("SELECIONE A QUANTIDADE DE PRODUTOS");
                        $(".quantity").click(function() {
                            $('button.single_add_to_cart_button.button').removeClass("botinativo");
                            $('.valorescompra').css('display', 'block');


                            $('.single_add_to_cart_button').html('COMPRAR');
                            $(".above_cart_button").css('display', 'none');
                        });

                    <?php }; ?>


                    <?php
                    if ($_GET['q']) {
                        $preqty = $_GET['q'];
                        // $calibragem = $_GET['c'];
                        $woocommerce->cart->empty_cart();
                    ?>

                    $.removeCookie('aberto');
                    $.removeCookie('quantidade');

                    <?php
                    } else {
                        if (isset($_COOKIE['aberto'])) {
                            $preqty = $_COOKIE['quantidade'];
                        }
                    }

                    if ($preqty) { ?>
                        jQuery(document).ready(function($) {



                            var preqty = jQuery('input.input-text.qty.text');
                            preqty.val('<?php echo $preqty ?>');

                            firstClick = false;
                            $('.comprawhats').css('display', 'inline-block');
                            $('.area-form-produto').css('display', 'none');
                            $('button.single_add_to_cart_button.button').css('display', 'block');
                            $('.valorescompra').css('display', 'block');
                            //verificar
                            $('button.single_add_to_cart_button.button.alt').addClass('add_cart_kit_pre');
                            $('.single_add_to_cart_button').html('COMPRAR');
                            $(".above_cart_button").css('display', 'none');
                            $('.quantidades-1890 input[type="button"]').click();
                            setTimeout(function() {
                                $('.quantity input[type="button"]').click();
                            }, 100);
                        });
                    <?php }; ?>
    <?php }; ?>
    <?php // TERMINA O IF - FXM ?>


    <?php //INICIA O IF - KITLIFT ?>
    <?php echo get_term('product_cat'); ?>
    <?php if (has_term(['Kit Lift'], 'product_cat')) { ?>

                    <?php $btnKit = get_post_meta(get_the_ID(), 'botao-kitlift', true);

                    if ($btnKit == "orcamento") { ?>

                        $('button.single_add_to_cart_button.button.alt').css('padding', '12px 25px 12px 101px');
                        $('button.single_add_to_cart_button.button.alt').css('font-size', '21px');

                        if (jQuery("div#gform_confirmation_message_19").length) {
                            firstClick = false;
                            $('.area-form-produto').css('display', 'block');
                            $('button.single_add_to_cart_button.button.alt').html("ADICIONAR AO CARRINHO");
                            $('.valorescompra').css('display', 'block');
                            $('.single_add_to_cart_button').html('COMPRAR');
                            jQuery(".above_cart_button").css('display', 'block');
                        }

                        $('button.single_add_to_cart_button.button.alt').html("CALCULAR VALOR");

                        $('#gform_submit_button_19').click(function() {
                            var controleQua = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_produto').html()).val()).slice(-2);
                            var controleVal = jQuery('span#valor-total').html();
                            if (controleQua == 0 || controleVal == 0) {
                                alert("Você precisa selecionar uma quantidade diferente de 0");
                                return false;
                            }


                            // cria botão whatsapp
                            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor')) { ?>
                                jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"></div>');
                                // jQuery('.avia_textblock.area-form-produto').append('<div class="gerawhats"><button id="gerawhats">GERAR LINK PARA WHATSAPP</button></div>');
                                // jQuery(document).on('click', '#gerawhats', function(){
                                var infoDate = new Date();
                                var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                                var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');
                                var cliente = jQuery("#input_19_1").val();
                                var emailcliente = jQuery("#input_19_3").val();
                                var telcliente = jQuery("#input_19_2").val();
                                var veiculo = jQuery("#input_19_20").val();
                                var quantidade = jQuery("#input_19_19").val();
                                var valortotal = jQuery('span#valor-total').html().replace(",", ".");
                                var valortotal = Number(valortotal);
                                var vardesconto = <?php echo get_option('descontoavista'); ?>;
                                var desconto = '(' + vardesconto + '% de desconto)';
                                var totalDesconto = (valortotal - (valortotal * (vardesconto / 100))).toFixed(2).replace(".", ",");
                                var condicoes = jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '|');
                                var condicoes = condicoes.replace(/(\r\n|\n|\r)/gm, "")
                                var condicoes = condicoes.replace(/(<([^>]+)>)/ig, "");

                                var valortotal = valortotal.toFixed(2).replace(".", ",");

                                <?php
                                $whatsappX = get_option('whatsapp');
                                if (preg_match('/(\d{2})(\d{2})(\d{5})(\d{4})$/', $whatsappX,  $matches)) {
                                    $whatsappVisualX = $matches[2] . ' ' . $matches[3] . '.' . $matches[4];
                                }
                                ?>
                                var whatsapp1 = '<?php echo $whatsappX; ?>';
                                var whatsapp2 = '<?php echo $whatsappVisualX; ?>';


                                // CRIA LINK DO ORÇAMENTO
                                var long_url = '?tipo=kitlift&ntipo=KIT Lift&tl=' + jQuery("#input_19_13").val() + '&valor2=' + valortotal + '&ti=' + jQuery("#input_19_20").val() + '&d=' + myDateString + '&c=' + jQuery("#input_19_1").val() + '&mail=' + jQuery("#input_19_3").val() + '&des=' + desconto + '&todes=' + totalDesconto + '&w1=' + whatsapp1 + '&w2=' + whatsapp2 + '&foto2=' + imagem1 + '&qU=' + quantidade + '&link=<?php echo $current_url; ?>' + '&con=' + condicoes;

                                long_url = long_url.replace(/\s/g, "%20");


                                // CRIA LINK REDUZIDO
                                let linkRequest = {
                                    destination: 'https://offlimits.com.br/orcamento-padrao/' + long_url,
                                    domain: {
                                        fullName: "rebrand.ly"
                                    }
                                }
                                let requestHeaders = {
                                    "Content-Type": "application/json",
                                    "apikey": "f1c72905e5764ea1bdf718eec7c26f07",
                                }
                                $.ajax({
                                    url: "https://api.rebrandly.com/v1/links",
                                    type: "post",
                                    data: JSON.stringify(linkRequest),
                                    headers: requestHeaders,
                                    dataType: "json",
                                    success: (link) => {
                                        console.log(`Long URL was ${link.destination}, short URL is ${link.shortUrl}`);
                                        var long_url = 'https://' + (`${link.shortUrl}`);
                                        console.log('link reduzido: ' + long_url);


                                        // CRIA TEXTO DO WHATSAPP

                                        var texto = 'Olá, ' + cliente + '\n\n Obrigado pelo seu interesse por nossos produtos. Segue abaixo, o orçamento conforme informações fornecidas. A efetivação da sua compra pode ser feita diretamente no site ou por telefone.\n\n ------\n\n*ORÇAMENTO*\n• VEÍCULO: ' + veiculo + '\n• ORÇAMENTO EMITIDO EM: ' + myDateString + '\n\n • *[ ' + quantidade + ' ]* KIT Lift | R$ ' + valortotal + ' \n\n • *TOTAL:* R$ ' + totalDesconto + ' ' + desconto + '\n\n VEJA SEU ORÇAMENTO COMPLETO: ' + long_url;

                                        texto = window.encodeURIComponent(texto);
                                        var urlWhatsapp = 'https://web.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;
                                        var urlWhatsapp2 = 'https://api.whatsapp.com/send?phone=55' + telcliente + '&text=' + texto;

                                        // MONTA E EXIBE BOTÕES
                                        jQuery('#Enviawhatsapp').remove();
                                        jQuery('#Enviawhatsapp2').remove();
                                        jQuery('.gerawhats').append('<a target="_blank" class="desktop" id="Enviawhatsapp">ENVIAR POR WHATSAPP</a>');
                                        jQuery('.gerawhats').append('<a target="_blank" class="mobile" id="Enviawhatsapp2">ENVIAR POR WHATSAPP</a>');
                                        $('#Enviawhatsapp').attr('href', urlWhatsapp);
                                        $('#Enviawhatsapp2').attr('href', urlWhatsapp2);

                                    }
                                }); // END REBRAND
                                //          });
                            <?php } ?>
                            // end whatsapp
                        }) // Fim - $('#gform_submit_button_19').click(function()


                        jQuery("ul#gform_fields_19 input").change(function() {
                            jQuery('.comprawhats2').addClass('comprawhats');
                            jQuery('input#input_59_1').val(jQuery('input#input_19_1').val());
                            jQuery('input#input_59_3').val(jQuery('input#input_19_2').val());
                            jQuery('input#input_59_2').val(jQuery('input#input_19_3').val());
                        });


                        $('button.single_add_to_cart_button.button').click(function() {
                            var controle = (jQuery('input.input-text.qty.text.qty-' + jQuery('#id_produto').html()).val()).slice(-2);
                            if (controle != 0) {
                                if (firstClick) {
                                    event.preventDefault();
                                    $('.area-form-produto').css('display', 'block');
                                    $('button.single_add_to_cart_button.button').css('display', 'none');
                                    $('form.cart').addClass('no-after');
                                    var idProduto = jQuery('#id_produto').html();
                                    var quantProduto = ('0' + jQuery('input.input-text.qty.text.qty-' + idProduto).val()).slice(-2);
                                    // jQuery("#input_19_10").val(jQuery('select.wccpf-field[name=opes_de_calibragem_] option:selected').html());
                                    jQuery("#input_19_12").val(jQuery('span#valor-total').html());
                                    jQuery("#input_19_13").val(jQuery('span#valor-desconto').html());


                                    jQuery("#input_19_28").val('<?php echo $whatsappVisual; ?>');
                                    jQuery("#input_19_27").val('<?php echo $whatsapp; ?>');


                                    jQuery("#input_19_26").val('<?php if (get_option('fretegratis')) {
                                        echo 'GRÁTIS';
                                    } else {
                                        echo 'continuar a compra para calcular';
                                    } ?>');


                                    <?php //criação da data
                                    ?>
                                    var infoDate = new Date();
                                    var myDateString = ('0' + infoDate.getDate()).slice(-2) + '/' + ('0' + (infoDate.getMonth() + 1)).slice(-2) + '/' + infoDate.getFullYear();
                                    jQuery("#input_19_14").val(myDateString);
                                    jQuery("#input_19_18").val(jQuery('h1.av-special-heading-tag').html());
                                    jQuery("#input_19_19").val(quantProduto);
                                    <?php //pegando veiculo
                                    ?>
                                    jQuery("#input_19_20").val(jQuery('.av-woo-purchase-button h3.av-special-heading-tag').html());



                                    var qtProduto = jQuery('input.input-text.qty.text.qty-' + idProduto).val();

                                    <?php
                                    global $wp;
                                    $current_url = home_url(add_query_arg(array(), $wp->request));
                                    ?>

                                    // var calibragemval = jQuery('select.wccpf-field[name=opes_de_calibragem] option:selected').val();

                                    var link = '<?php echo $current_url; ?>?q=' + qtProduto;
                                    jQuery('#input_19_21').val(link);

                                    var imagem1 = jQuery('.avia-gallery-thumb a:eq( 0 )').attr('data-prev-img');

                                    var sourceThumbnail = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide.swiper-slide-active > div > a.jet-woo-product-gallery__image-link > img')

                                    if(!sourceThumbnail)
                                        sourceThumbnail = document.querySelector('.swiper-wrapper > div.jet-woo-product-gallery__image-item.featured.swiper-slide > div.jet-woo-product-gallery__image > a.jet-woo-product-gallery__image-link > img')

                                    if(sourceThumbnail){
                                        imagem1 = sourceThumbnail.src
                                    }

                                    jQuery('#input_19_38').val(imagem1);

                                    jQuery("#input_19_25").html(jQuery(".listaformaspagamento").html().replace(new RegExp('<br>', 'g'), '\n'));

                                    var valdesconto = jQuery('.colunaprecocondicaoobs').html();
                                    jQuery('#input_19_24').val(valdesconto);

                                    $('button.single_add_to_cart_button.button.alt').html('ADICIONAR AO CARRINHO');

                                    var position = $("#gform_fields_19").offset().top - 120;
                                    // $('html, body').animate({
                                    //     scrollTop: position
                                    // }, 300);
                                    // console.log('clicou 19');

                                } // Fim - if (firstClick)
                            } else {
                                alert("Você precisa selecionar uma quantidade maior que 0");
                                return false;
                            }
                        });

                        jQuery(document).bind('gform_confirmation_loaded', function(event, formId) {

                            if (formId == 19) {
                                console.log('clicou para enviar o form');

                                $('.area-form-produto').css('display', 'block');
                                $('.comprawhats').css('display', 'inline-block');
                                firstClick = false;
                                var deslocamento = jQuery('button.single_add_to_cart_button.button').offset().top - 120;
                                // $('html, body').animate({
                                //     scrollTop: deslocamento
                                // }, 'slow');

                                if (jQuery("div#gform_confirmation_message_19").length) {
                                    $('button.single_add_to_cart_button.button.alt').css('display', 'block');
                                    $('form.cart').removeClass('no-after');
                                    $('.valorescompra').css('display', 'block');
                                    $('button.single_add_to_cart_button.button.alt').addClass('cart_kit');
                                    //                          gtag_report_conversion();

                                    $('.single_add_to_cart_button').html('COMPRAR');
                                    jQuery(".above_cart_button").css('display', 'block');

                                    var qtyProduto = jQuery('input.input-text.qty.text.qty').val();
                                    $.cookie('aberto', 'sim', {
                                        expires: 3
                                    });
                                    $.cookie('quantidade', qtyProduto, {
                                        expires: 3
                                    });


                                    var deslocamento = jQuery('.valorescompra.honey-size').offset().top - 120;
                                    // $('html, body').animate({
                                    //     scrollTop: deslocamento
                                    // }, 300);

                                }

                            } // Fim - if (formId == 19)

                        }); // Fim - jQuery(document).bind('gform_confirmation_loaded', function(event, formId)


                    <?php } ?> // Fim - if ($btnKit == "orcamento")


                    // auto preencimento
                    // var cookieaberto = $.cookie('aberto');
                    var cookieaberto = false;
                    var cookieqty = $.cookie('quantidade');

                    console.log(cookieaberto);
                    console.log(cookieqty);

                    <?php
                    $aberto = $_GET['a'];
                    global $woocommerce;
                    $count = $woocommerce->cart->cart_contents_count;
                    if ($count > 0) {
                        $aberto = 'comprador'; ?> console.log('carrinho cheio');
                    <?php  } else {  ?> console.log('carrinho vazio');
                    <?php }    ?>

                    <?php // if(has_term( 'Configuravel', 'product_cat' )){ $aberto = 'configuravel'; console.log('carrinho cheio'); } else { console.log('nao configuravel'); } ?>

                    <?php if (($abertoo)) { ?>

                        firstClick = false;
                        $('.comprawhats').css('display', 'inline-block');
                        $('.area-form-produto').css('display', 'none');
                        $('button.single_add_to_cart_button.button').css('display', 'block');
                        $('button.single_add_to_cart_button.button').addClass("botinativo");
                        $('button.single_add_to_cart_button.button.alt').html("SELECIONE A QUANTIDADE DE PRODUTOS");
                        $(".quantity").click(function() {
                            $('button.single_add_to_cart_button.button').removeClass("botinativo");
                            $('.valorescompra').css('display', 'block');
                            $('.single_add_to_cart_button').html('COMPRAR');
                            $(".above_cart_button").css('display', 'none');
                        });

                    <?php }; ?>

                    <?php

                    if ($_GET['q']) {
                        $preqty = $_GET['q'];
                        // $calibragem = $_GET['c'];
                        $woocommerce->cart->empty_cart();
                    ?>
                        $.removeCookie('aberto');
                        $.removeCookie('quantidade');
                    <?php

                    } else {
                        if (isset($_COOKIE['aberto'])) {
                            $preqty = $_COOKIE['quantidade'];
                        }
                    }

                    if ($preqty) { ?>
                        jQuery(document).ready(function($) {

                            var preqty = jQuery('input.input-text.qty.text');
                            preqty.val('<?php echo $preqty ?>');

                            firstClick = false;
                            $('.comprawhats').css('display', 'inline-block');
                            $('.area-form-produto').css('display', 'none');
                            $('button.single_add_to_cart_button.button').css('display', 'block');
                            $('.valorescompra').css('display', 'block');
                            $('button.single_add_to_cart_button.button.alt').addClass('add_cart_kit_pre');
                            $('.single_add_to_cart_button').html('COMPRAR');
                            $(".above_cart_button").css('display', 'none');
                            $('.quantidades-1890 input[type="button"]').click();
                            setTimeout(function() {
                                $('.quantity input[type="button"]').click();
                            }, 100);
                        });
                    <?php }; ?>
    <?php }; ?>
    <?php // TERMINA O IF - KITLIFT  ?>

});
<?php // TERMINA - Codes Forms Orçamentos ?>

</script>

<script>
    var quant_control = jQuery('.single-product .quantity');
    if (quant_control.length == 1) {
        jQuery('.single-product .wccpf-fields-group-1').addClass('info-double-space');
    }
</script>

<script type="text/javascript" async src="https://d335luupugsy2.cloudfront.net/js/loader-scripts/76fc7a7c-8c98-4722-a95a-01c6633764d0-loader.js"></script>

<script type="text/javascript" src="https://d335luupugsy2.cloudfront.net/js/integration/stable/rd-js-integration.min.js"></script>

</body>
</html>