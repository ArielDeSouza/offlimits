<?php

/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see    https://docs.woocommerce.com/document/template-structure/
 * @author WooThemes
 * @package WooCommerce/Templates
 * @version     3.4.0
 */



if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

if (!$product->is_purchasable()) {
    return;
}

// informa se o desconto é por % ou R$
$tipoDesconto = 'real'; // opções: 'real' 'porcentagem'

//adiciona o valor do desconto (sem o %) - linha 174 < ?php echo $vdesconto ? >
if (has_term('promo', 'product_cat')) { 
    $vdesconto  = 300;   // tem a categoria Promo, recebe o desconto. O tipo do desconto foi definido na variável $tipoDesconto;
} else {
    $vdesconto  = 0;   // todos os demais
}

$vjuros     = 1;    // adiciona o valor dos juros da parcela - linha 384 < ?php echo $vjuros ? >
$vjurostax  = $vjuros / 100; //calcula a taxa de juros < ?php echo $vjurostax ? >

?>

<?php // INICIA O IF - Kit Lift ?>
<?php if( has_term( 'kit-lift', 'product_cat' ) ) {
    $product_id = get_the_ID();
    $product_price = $product->get_price();

    // Availability
    $availability      = $product->get_availability();
    $availability_html = empty($availability['availability']) ? '' : '<p class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>';
    echo apply_filters('woocommerce_stock_html', $availability_html, $availability['availability'], $product);
    ?>

    <?php if ($product->is_in_stock()) : ?>

        <?php do_action('woocommerce_before_add_to_cart_form'); ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var preco_global = '';
            })
        </script>

        <!--Ocultar campos do Formulário-->
        <style>
            #field_15_30,
            #field_15_38,
            #field_15_36,
            #field_15_31,
            #field_15_35,
            #field_15_34,
            #field_15_33,
            #field_15_32,
            #field_15_37 {
                display: none;
            }

			#field_19_29,
			#field_19_38,
			#field_19_37,
			#field_19_30,
			#field_19_35,
			#field_19_34,
			#field_19_33,
			#field_19_32,
			#field_19_31,
			#field_19_36 {
				display: none;
			}
        </style>

        <div style="padding-bottom:10px;color:#333333;" class="av-special-heading av-special-heading-h3 custom-color-heading blockquote modern-quote avia-builder-el-12  el_before_av_textblock  avia-builder-el-first  wine-size green-left">

            <div class="special-heading-border">
                <div class="special-heading-inner-border" style="border-color:#333333"></div>
            </div>

        </div>

        <form class="cart" method="post" enctype='multipart/form-data'>
            <div class="quantidades-<?php echo $product_id ?> produtoindice1">

                <div class="honey-size label" style="float: left;clear: both;min-width: 100px;">
                    <h3 for="product-<?php echo $product_id; ?>" style="margin-top: 10px;margin-right: 15px;margin-bottom: 10px;font-weight: normal">
                        QUANTIDADE
                    </h3>
                </div>

                <?php
                $preco_smart = get_post_meta($product->id, "wccaf_preo_smart_kit_lift", true);

                if (!$product->is_sold_individually()) {
                    woocommerce_quantity_input(array(
                        'min_value'   => apply_filters('woocommerce_quantity_input_min', 0, $product),
                        'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product),
                        'input_value' => (isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 0)
                    ));
                }
                ?>

                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->id); ?>" />
            </div><br>

            <div style="display:none" id="id_produto"></div>

            <?php do_action('woocommerce_before_add_to_cart_button'); ?>

            <script type="text/javascript">
                var global_price = [];
                var total_price_<?php echo $product_id ?> = {};
                var totalFinal = 0;
                var totalDesconto = 0;
                var totalParcela = 0;
                var descontoavista = <?php echo $vdesconto ?>;
                var taxajuro = <?php echo $vjuros ?>;
                var jurostax = <?php echo $vjurostax ?>;
            /*01*/
                var parcela1 = 0;
                var parcela2 = 0;
                var parcela3 = 0;
                var parcela4 = 0;
                var parcela5 = 0;
                var parcela10 = 0;
                var temp = 0;
                var valor = 0;
                var quantidade = 0;

                jQuery(document).ready(function($) {
                    $('#id_produto').html(<?php echo $product_id ?>);

                    $(document).on('click', '.quantidades-<?php echo $product_id ?> input[type="button"]', function() {
                        total_price_<?php echo $product_id ?> = {
                            valor: $('#preco-<?php echo $product_id ?>').val(),
                            id: <?php echo $product_id ?>
                        };

                        if ($('.wccpf-field[name="selecione_o_tipo_de_amortecedor_"] option:selected').val() === 'smart') {
                            valor = '<?php echo $preco_smart ?>';
                        } else {
                            valor = $('#preco-<?php echo $product_id ?>').val();
                        }

                        quantidade = $('.qty-<?php echo $product_id ?>').val();
                        total_price_<?php echo $product_id ?>.valor = valor * quantidade;
                        valortotal = valor * quantidade;
                        total = total_price_<?php echo $product_id ?>.valor.toFixed(2).replace(".", ",");
                        totalLimpo = (total_price_<?php echo $product_id ?>.valor);

                        <?php if ($tipoDesconto == 'real') { ?>
                            totalDesconto = (totalLimpo - descontoavista).toFixed(2).replace(".", ",");
                        <?php } ?>
                        <?php if ($tipoDesconto == 'porcentagem') { ?>
                            totalDesconto = (totalLimpo - (totalLimpo * (descontoavista / 100))).toFixed(2).replace(".", ",");
                        <?php } ?>


                        <?php if ($vjurostax > 0) { ?>
                            totalParcela = (total_price_<?php echo $product_id ?>.valor / 5).toFixed(2).replace(".",",");
                            parcela1 = (valortotal).toFixed(2).replace(".", ",");
                            parcela2 = (valortotal / 2).toFixed(2).replace(".", ",");
                            parcela3 = (valortotal / 3).toFixed(2).replace(".", ",");
                            parcela4 = (valortotal / 4).toFixed(2).replace(".", ",");
                            parcela5 = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela10 = ((valortotal / 10) + (valortotal * jurostax)).toFixed(2).replace(".", ",");

                        <?php } else { ?>
                            totalParcela = (total_price_<?php echo $product_id ?>.valor / 10).toFixed(2).replace(".",",");
                            parcela1 = (valortotal).toFixed(2).replace(".", ",");
                            parcela2 = (valortotal / 2).toFixed(2).replace(".", ",");
                            parcela3 = (valortotal / 3).toFixed(2).replace(".", ",");
                            parcela4 = (valortotal / 4).toFixed(2).replace(".", ",");
                            parcela5 = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela10 = (valortotal / 10).toFixed(2).replace(".", ",");
                        <?php } ?>

                        $('#valor-total').html(total);
                        $('#valor-total2').html(total);
                        $('#valor-total3').html(totalDesconto);
                        $('#valor-desconto').html(totalDesconto);
                        $('#valor-pacela').html(totalParcela);
                        $('#valor-pacelas10sj').html(totalParcela);
                        $('#input_19_29').val(descontoavista);
                        $('#input_19_37').val(totalDesconto);
                        $('#input_19_30').val(parcela1);
                        $('#input_19_35').val(parcela2);
                        $('#input_19_32').val(parcela3);
                        $('#input_19_34').val(parcela4);
                        $('#input_19_33').val(parcela5);
                        $('#input_19_31').val(parcela10);

                        <?php if ($vjurostax > 0) { ?>
                            $('#input_19_36').val('com juros');
                        <?php } else { ?>
                            $('#input_19_36').val('sem juros');
                        <?php } ?>

                        $('#valor-1parcela').html(parcela1);
                        $('#valor-2parcela').html(parcela2);
                        $('#valor-3parcela').html(parcela3);
                        $('#valor-4parcela').html(parcela4);
                        $('#valor-5parcela').html(parcela5);
                        $('#valor-10parcela').html(parcela10);

                    })

                    $(document).on('change', '.wccpf-field[name="selecione_o_tipo_de_amortecedor_"]', function() {
                        if ($('.wccpf-field[name="selecione_o_tipo_de_amortecedor_"] option:selected').val() === 'smart') {
                            valor = '<?php echo $preco_smart ?>';
                        } else {
                            valor = $('#preco-<?php echo $product_id ?>').val();
                        }
                        quantidade = $('.qty-<?php echo $product_id ?>').val();
                        valortotal = valor * quantidade;
                        total = valortotal.toFixed(2).replace(".", ",");

                        <?php if ($tipoDesconto == 'real') { ?>
                            totalDesconto = (total - descontoavista).toFixed(2).replace(".", ",");
                        <?php } ?>
                        <?php if ($tipoDesconto == 'porcentagem') { ?>
                            totalDesconto = (total - (total * (descontoavista / 100))).toFixed(2).replace(".", ",");
                        <?php } ?>                        

                        <?php if ($vjurostax > 0) { ?>
                            totalParcela = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela1 = (total).toFixed(2).replace(".", ",");
                            parcela2 = (total / 2).toFixed(2).replace(".", ",");
                            parcela3 = (total / 3).toFixed(2).replace(".", ",");
                            parcela4 = (total / 4).toFixed(2).replace(".", ",");
                            parcela5 = (total / 5).toFixed(2).replace(".", ",");
                            parcela10 = ((total / 10) + (total * jurostax)).toFixed(2).replace(".", ",");
                        <?php } else { ?>
                            totalParcela = (valortotal / 10).toFixed(2).replace(".", ",");
                            parcela1 = (total).toFixed(2).replace(".", ",");
                            parcela2 = (total / 2).toFixed(2).replace(".", ",");
                            parcela3 = (total / 3).toFixed(2).replace(".", ",");
                            parcela4 = (total / 4).toFixed(2).replace(".", ",");
                            parcela5 = (total / 5).toFixed(2).replace(".", ",");
                            parcela10 = (total / 10).toFixed(2).replace(".", ",");
                        <?php } ?>

                        $('#valor-total').html(total);
                        $('#valor-total2').html(total);
                        $('#valor-total3').html(totalDesconto);
                        $('#valor-desconto').html(totalDesconto);
                        $('#valor-pacela').html(totalParcela);
						$('#valor-pacelas10sj').html(totalParcela);
                        $('#valor-pacelas10sj').html(totalParcela);
                        $('#input_19_29').val(descontoavista);
                        $('#input_19_37').val(totalDesconto);
                        $('#input_19_30').val(parcela1);
                        $('#input_19_35').val(parcela2);
                        $('#input_19_32').val(parcela3);
                        $('#input_19_34').val(parcela4);
                        $('#input_19_33').val(parcela5);
                        $('#input_19_31').val(parcela10);

                        <?php if ($vjurostax > 0) { ?>
                            $('#input_19_36').val('com juros');
                        <?php } else { ?>
                            $('#input_19_36').val('sem juros');
                        <?php } ?>

                        $('#valor-1parcela').html(parcela1);
                        $('#valor-2parcela').html(parcela2);
                        $('#valor-3parcela').html(parcela3);
                        $('#valor-4parcela').html(parcela4);
                        $('#valor-5parcela').html(parcela5);
                        $('#valor-10parcela').html(parcela10);

                    })
                });
            </script>



            <input id="preco-<?php echo $product_id ?>" style="visibility: hidden; display: none;" value="<?php echo $product_price; ?>">

 			<div class="valorescompra honey-size" style="display:none; margin: 20px 0 0;padding: 5px;">

                <h2>ORÇAMENTO:</h2>
            </div>

            <div class="valorescompra" style="display:none; margin: -10px 0 30px; padding: 5px;background: #fff;">

                <div class="honey-size">

                    <p class="precoparcelado"><span style="width: 20%;">TOTAL:</span> <span>R$ <span id="valor-total">0</span>
                        </span><?php if (get_option('fretegratis')) {
                                    echo '<b> (FRETE GRÁTIS)</b>';
                                }; ?>
                    </p>

                </div>

                <div>

                    <?php if (has_term('promo', 'product_cat')) { ?>
                    <div class="honey-size colunapreco1">
                        <p class="colunaprecocondicao">R$ <span id="valor-desconto">0</span> à vista</p>
                        <p class="colunaprecocondicaoobs">

                        <?php if ($tipoDesconto == 'real') {
                            echo "(" . $vdesconto . "% de desconto)";
                        }     if ($tipoDesconto == 'porcentagem') {
                            echo "(R$" . $vdesconto . ",00 de desconto)";
                        } ?>                        

                        </p>
                    </div>
                    <?php }; ?>
                  
					
					<div class="honey-size colunapreco2">
                        <?php if ($vjurostax > 0) { ?>
                            <p class="colunaprecocondicao">até 5x de R$ <span id="valor-pacela">0</span> sem juros</p>
                            <p class="colunaprecocondicaoobs">ou em até 10x com juros de <?php echo $vjuros ?>% a.m.</p>
                        <?php } else { ?>
                            <p class="colunaprecocondicao">até 10x de R$ <span id="valor-pacela">0</span> </p>
                            <p class="colunaprecocondicaoobs">sem juros</p>
                        <?php } ?>

                    </div>

                </div>

            </div>
            <button type="submit" class="single_add_to_cart_button button alt">Comprar</button>

            <a href="https://api.whatsapp.com/send?phone=<?php echo get_option('whatsapp'); ?>&amp;text=Olá%20estou%20interessado%20no%20<?php echo str_replace('+', '%20', $product->get_title()); ?>" class="comprawhats mobile" style="display:none;">Comprar pelo Whatsapp</a>

            <?php do_action('woocommerce_after_add_to_cart_button'); ?>
        </form>

        <div id="armored_website" class="blindadoproduto" style="margin-top:5px; margin-bototm:15px;">
            <param id="aw_preload" value="true" />
            <param id="aw_use_cdn" value="true" />
        </div>

        <script type="text/javascript" src="//cdn.siteblindado.com/aw.js"></script>

        <?php
        echo get_term('product_cat');

        if( has_term( 'kit-lift', 'product_cat' ) ) { ?>

            <div class="above_cart_button" style="margin-top: 23px; font-style: initial; width: 100%; text-align: left; font-size: 13px; display: none;">
                ENVIAMOS O ORÇAMENTO PARA SEU EMAIL.
                <?php if (get_option('fretegratis')) {
                    echo '<b> (FRETE GRÁTIS)</b>';
                } else {
                    echo 'O FRETE SERÁ CALCULADO NA PRÓXIMA TELA.';
                } ?>.
            </div>

            <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">

                <div class="avia_textblock area-form-produto" itemprop="text">
                    <?php echo do_shortcode('[gravityform id=19 title=false ajax=true]'); ?>
                </div>

            </section>

        <?php } ?>

        <div class="valorescompra" style="display:none; margin: 20px 0 30px; padding: 5px; text-transform: uppercase; font-size: 15px;">

            <div class="honey-size">

                Formas de pagamento:

                <style>
                    .listaformaspagamento {
                        font-family: 'Oswald', sans-serif;
                        font-size: 13px;
                        line-height: 30px !important;
                        letter-spacing: 0.05em !important;
                        font-weight: normal;
                        text-decoration: none;
                    }

                    #field_19_25 {
                        display: none;
                    }
                </style>


                <div class="listaformaspagamento">
                    <?php if (has_term('promo', 'product_cat')) { ?>
                    <p class="precoparcelado" style="margin-bottom: -30px;"><span style="width: 20%;">À vista com <?php echo $vdesconto ?>% de desconto:</span> <span>R$ <span id="valor-total3">0</span></span></p><br>
                    <?php }; ?>
                    <span id="parcela1">1 parcela de: R$ <span id="valor-1parcela">0</span> sem juros</span><br>
                    <span id="parcela2">2 parcelas de: R$ <span id="valor-2parcela">0</span> sem juros</span><br>
                    <span id="parcela3">3 parcelas de: R$ <span id="valor-3parcela">0</span> sem juros</span><br>
                    <span id="parcela4">4 parcelas de: R$ <span id="valor-4parcela">0</span> sem juros</span><br>
                    <span id="parcela5">5 parcelas de: R$ <span id="valor-5parcela">0</span> sem juros</span><br>
                    <?php if ($vjurostax > 0) { ?>
                        <span id="parcela6">Até 10 parc. de: R$ <span id="valor-10parcela">0</span> c/ juros de <?php echo $vjuros ?>% a.m. </span><br>
                    <?php } else { ?>
                        <span id="parcela6">10 parcelas de: R$ <span id="valor-10parcela">0</span> sem juros</span><br>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php do_action('woocommerce_after_add_to_cart_form'); ?>

        <?php if (current_user_can('editor') || current_user_can('administrator')) {
            global $wp;
            $current_url = home_url(add_query_arg(array(), $wp->request));
        ?>

            <a id="gerarlink">GERAR LINK PARA CLIENTE</a>

            <script type="text/javascript">
                jQuery(document).ready(function($) {

                    $("#gerarlink").click(function() {
                        //var Tipocalibragem = $( "#top.single-product select.wccpf-field option:selected" ).val();
                        $('#linkgerado').html('<?php echo $current_url; ?>?q=' + quantidade);
                    });

                });
            </script>

            <p id="linkgerado"></p>
        <?php }
    endif; ?>
<?php // TERMINA O IF - Kit Lift ?>

<?php // INICIA O ELSEIF - FXM ?>
<?php } elseif (has_term('fxm', 'product_cat')) {

    $product_id = get_the_ID();
    $product_price = $product->get_price();

    // Availability
    $availability      = $product->get_availability();
    $availability_html = empty($availability['availability']) ? '' : '<p class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>';

    echo apply_filters('woocommerce_stock_html', $availability_html, $availability['availability'], $product);
	if ($product->is_in_stock()) : do_action('woocommerce_before_add_to_cart_form'); ?>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var preco_global = '';
            })
        </script>
        <style>
            #field_72_29,
            #field_72_38,
            #field_72_37,
            #field_72_30,
            #field_72_35,
            #field_72_34,
            #field_72_33,
            #field_72_32,
            #field_72_31,
            #field_72_36 {
            display: none;
            }
        </style>

        <div style="padding-bottom:10px;color:#333333;" class="av-special-heading av-special-heading-h3 custom-color-heading blockquote modern-quote  avia-builder-el-12  el_before_av_textblock  avia-builder-el-first  wine-size green-left">

            <div class="special-heading-border">
                <div class="special-heading-inner-border" style="border-color:#333333"></div>
            </div>

        </div>

        <form class="cart" method="post" enctype='multipart/form-data'>
            <div class="quantidades-<?php echo $product_id ?> produtoindice1">
                <div class="honey-size label" style="float: left;clear: both;min-width: 100px;">
                    <h3 for="product-<?php echo $product_id; ?>" style="margin-top: 10px;margin-right: 15px;margin-bottom: 10px;font-weight: normal">
                        QUANTIDADE
                    </h3>
                </div>

                <?php
                $preco_smart = get_post_meta($product->id, "wccaf_preo_smart_kit_lift", true);

                if (!$product->is_sold_individually()) {

                    woocommerce_quantity_input(array(
                        'min_value'   => apply_filters('woocommerce_quantity_input_min', 0, $product),
                        'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product),
                        'input_value' => (isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 0)
                    ));
                } ?>

                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->id); ?>" />

            </div><br>

            <div style="display:none" id="id_produto"></div>

            <?php do_action('woocommerce_before_add_to_cart_button'); ?>

            <script type="text/javascript">
                var global_price = [];
                var total_price_<?php echo $product_id ?> = {};
                var totalFinal = 0;
                var totalDesconto = 0;
                var totalParcela = 0;
                var descontoavista = <?php echo $vdesconto ?>;
                var taxajuro = <?php echo $vjuros ?>;
                var jurostax = <?php echo $vjurostax ?>;
                var parcela1 = 0;
                var parcela2 = 0;
                var parcela3 = 0;
                var parcela4 = 0;
                var parcela5 = 0;
                var parcela10 = 0;
                var temp = 0;
                var valor = 0;
                var quantidade = 0;

                jQuery(document).ready(function($) {
                    $('#id_produto').html(<?php echo $product_id ?>);
                    $(document).on('click', '.quantidades-<?php echo $product_id ?> input[type="button"]', function() {
                        total_price_<?php echo $product_id ?> = {
                            valor: $('#preco-<?php echo $product_id ?>').val(),
                            id: <?php echo $product_id ?>
                        };

                        if ($('.wccpf-field[name="selecione_o_tipo_de_amortecedor_"] option:selected').val() === 'smart') {
                            valor = '<?php echo $preco_smart ?>';
                        } else {
                            valor = $('#preco-<?php echo $product_id ?>').val();
                        }

                        quantidade = $('.qty-<?php echo $product_id ?>').val();
                        total_price_<?php echo $product_id ?>.valor = valor * quantidade;
                        valortotal = valor * quantidade;
                        total = total_price_<?php echo $product_id ?>.valor.toFixed(2).replace(".", ",");
                        totalLimpo = (total_price_<?php echo $product_id ?>.valor);

                        <?php if ($tipoDesconto == 'real') { ?>
                            totalDesconto = (totalLimpo - descontoavista).toFixed(2).replace(".", ",");
                        <?php } ?>
                        <?php if ($tipoDesconto == 'porcentagem') { ?>
                            totalDesconto = (totalLimpo - (totalLimpo * (descontoavista / 100))).toFixed(2).replace(".", ",");
                        <?php } ?>


                        <?php if ($vjurostax > 0) { ?>
                            totalParcela = (total_price_<?php echo $product_id ?>.valor / 10).toFixed(2).replace(".",",");
                            parcela1 = (valortotal).toFixed(2).replace(".", ",");
                            parcela2 = (valortotal / 2).toFixed(2).replace(".", ",");
                            parcela3 = (valortotal / 3).toFixed(2).replace(".", ",");
                            parcela4 = (valortotal / 4).toFixed(2).replace(".", ",");
                            parcela5 = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela10 = ((valortotal / 10) + (valortotal * jurostax)).toFixed(2).replace(".", ",");
                        <?php } else { ?>
                            totalParcela = (total_price_<?php echo $product_id ?>.valor / 10).toFixed(2).replace(".",",");
                            parcela1 = (valortotal).toFixed(2).replace(".", ",");
                            parcela2 = (valortotal / 2).toFixed(2).replace(".", ",");
                            parcela3 = (valortotal / 3).toFixed(2).replace(".", ",");
                            parcela4 = (valortotal / 4).toFixed(2).replace(".", ",");
                            parcela5 = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela10 = (valortotal / 10).toFixed(2).replace(".", ",");
                        <?php } ?>

                        $('#valor-total').html(total);
                        $('#valor-total2').html(total);
                        $('#valor-total3').html(totalDesconto);
                        $('#valor-desconto').html(totalDesconto);
                        $('#valor-pacela').html(totalParcela);
                        $('#valor-pacelas10sj').html(totalParcela);
                        $('#input_72_29').val(descontoavista);
                        $('#input_72_37').val(totalDesconto);
                        $('#input_72_30').val(parcela1);
                        $('#input_72_35').val(parcela2);
                        $('#input_72_34').val(parcela3);
                        $('#input_72_33').val(parcela4);
                        $('#input_72_32').val(parcela5);
                        $('#input_72_31').val(parcela10);

                        <?php if ($vjurostax > 0) { ?>
                            $('#input_72_36').val('com juros');
                        <?php } else { ?>
                            $('#input_72_36').val('sem juros');
                        <?php } ?>

                        $('#valor-1parcela').html(parcela1);
                        $('#valor-2parcela').html(parcela2);
                        $('#valor-3parcela').html(parcela3);
                        $('#valor-4parcela').html(parcela4);
                        $('#valor-5parcela').html(parcela5);
                        $('#valor-10parcela').html(parcela10);
                    })



                    $(document).on('change', '.wccpf-field[name="selecione_o_tipo_de_amortecedor_"]', function() {
                        if ($('.wccpf-field[name="selecione_o_tipo_de_amortecedor_"] option:selected').val() === 'smart') {
                            valor = '<?php echo $preco_smart ?>';
                        } else {
                            valor = $('#preco-<?php echo $product_id ?>').val();
                        }
                        quantidade = $('.qty-<?php echo $product_id ?>').val();
                        valortotal = valor * quantidade;
                        total = valortotal.toFixed(2).replace(".", ",");

                        <?php if ($tipoDesconto == 'real') { ?>
                            totalDesconto = (total - descontoavista).toFixed(2).replace(".", ",");
                        <?php } ?>
                        <?php if ($tipoDesconto == 'porcentagem') { ?>
                            totalDesconto = (total - (total * (descontoavista / 100))).toFixed(2).replace(".", ",");
                        <?php } ?>                        

                        <?php if ($vjurostax > 0) { ?>
                            totalParcela = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela1 = (total).toFixed(2).replace(".", ",");
                            parcela2 = (total / 2).toFixed(2).replace(".", ",");
                            parcela3 = (total / 3).toFixed(2).replace(".", ",");
                            parcela4 = (total / 4).toFixed(2).replace(".", ",");
                            parcela5 = (total / 5).toFixed(2).replace(".", ",");
                            parcela10 = ((total / 10) + (total * jurostax)).toFixed(2).replace(".", ",");
                        <?php } else { ?>
                            totalParcela = (valortotal / 5).toFixed(2).replace(".", ",");
                            parcela1 = (total).toFixed(2).replace(".", ",");
                            parcela2 = (total / 2).toFixed(2).replace(".", ",");
                            parcela3 = (total / 3).toFixed(2).replace(".", ",");
                            parcela4 = (total / 4).toFixed(2).replace(".", ",");
                            parcela5 = (total / 5).toFixed(2).replace(".", ",");
                            parcela10 = (total / 10).toFixed(2).replace(".", ",");
                        <?php } ?>
						
                        $('#valor-total').html(total);
                        $('#valor-total2').html(total);
                        $('#valor-total3').html(totalDesconto);
                        $('#valor-desconto').html(totalDesconto);
                        $('#valor-pacela').html(totalParcela);
						$('#valor-pacelas10sj').html(totalParcela);						
						$('#input_72_29').val(descontoavista);
                        $('#input_72_37').val(totalDesconto);
                        $('#input_72_30').val(parcela1);
                        $('#input_72_35').val(parcela2);
                        $('#input_72_34').val(parcela3);
                        $('#input_72_33').val(parcela4);
                        $('#input_72_32').val(parcela5);
                        $('#input_72_31').val(parcela10);

                        <?php if ($vjurostax > 0) { ?>
                            $('#input_72_36').val('com juros');
                        <?php } else { ?>
                            $('#input_72_36').val('sem juros');
                        <?php } ?>

                        $('#valor-1parcela').html(parcela1);
                        $('#valor-2parcela').html(parcela2);
                        $('#valor-3parcela').html(parcela3);
                        $('#valor-4parcela').html(parcela4);
                        $('#valor-5parcela').html(parcela5);
                        $('#valor-10parcela').html(parcela10);

                    })

                });
            </script>



            <input id="preco-<?php echo $product_id ?>" style="visibility: hidden; display: none;" value="<?php echo $product_price; ?>">
            <div class="valorescompra honey-size" style="display:none; margin: 20px 0 0;padding: 5px;">
                <h2>ORÇAMENTO:</h2>
            </div>

            <div class="valorescompra" style="display:none; margin: -10px 0 30px; padding: 5px;background: #fff;">
                <div class="honey-size">

                    <p class="precoparcelado"><span style="width: 20%;">TOTAL:</span> <span>R$ <span id="valor-total">0</span>
                        </span><?php if (get_option('fretegratis')) {
                                    echo '<b> (FRETE GRÁTIS)</b>';
                                }; ?>
                    </p>

                </div>

                <div>

                    <?php if (has_term('promo', 'product_cat')) { ?>
                    <div class="honey-size colunapreco1">
                        <p class="colunaprecocondicao">R$ <span id="valor-desconto">0</span> à vista</p>
                        <p class="colunaprecocondicaoobs">

                        <?php if ($tipoDesconto == 'real') {
                            echo "(" . $vdesconto . "% de desconto)";
                        }     if ($tipoDesconto == 'porcentagem') {
                            echo "(R$" . $vdesconto . ",00 de desconto)";
                        } ?>                        

                        </p>
                    </div>
                    <?php }; ?>

                    <div class="honey-size colunapreco2">
                        <?php if ($vjurostax > 0) { ?>
                            <p class="colunaprecocondicao">até 5x de R$ <span id="valor-pacela">0</span> sem juros</p>
                            <p class="colunaprecocondicaoobs">ou em até 10x com juros de <?php echo $vjuros ?>% a.m.</p>
                        <?php } else { ?>
                            <p class="colunaprecocondicao">até 10x de R$ <span id="valor-pacela">0</span> </p>
                            <p class="colunaprecocondicaoobs"> sem juros</p>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="single_add_to_cart_button button alt">Comprar</button>
            <a href="https://api.whatsapp.com/send?phone=<?php echo get_option('whatsapp'); ?>&amp;text=Olá%20estou%20interessado%20no%20<?php echo str_replace('+', '%20', $product->get_title()); ?>" class="comprawhats mobile" style="display:none;">Comprar pelo Whatsapp</a>

            <?php do_action('woocommerce_after_add_to_cart_button'); ?>

        </form>



        <div id="armored_website" class="blindadoproduto" style="margin-top:5px; margin-bototm:15px;">
            <param id="aw_preload" value="true" />
            <param id="aw_use_cdn" value="true" />
        </div>

        <script type="text/javascript" src="//cdn.siteblindado.com/aw.js"></script>

            <div class="above_cart_button" style="margin-top: 23px; font-style: initial; width: 100%; text-align: left; font-size: 13px; display: none;">
                ENVIAMOS O ORÇAMENTO PARA SEU EMAIL.
                <?php if (get_option('fretegratis')) {
                    echo '<b> (FRETE GRÁTIS)</b>';
                } else {
                    echo 'O FRETE SERÁ CALCULADO NA PRÓXIMA TELA.';
                } ?>.
            </div>

            <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                <div class="avia_textblock area-form-produto" itemprop="text">
                    <?php echo do_shortcode('[gravityform id=72 title=false ajax=true]'); ?>
                </div>

            </section>

        <div class="valorescompra" style="display:none; margin: 20px 0 30px; padding: 5px; text-transform: uppercase; font-size: 15px;">

            <div class="honey-size">

                Formas de pagamento:

                <style>
                    .listaformaspagamento {
                        font-family: 'Oswald', sans-serif;
                        font-size: 13px;
                        line-height: 30px !important;
                        letter-spacing: 0.05em !important;
                        font-weight: normal;
                        text-decoration: none;
                    }

                    #field_72_25 {
                        display: none;
                    }
                </style>


                <div class="listaformaspagamento">
                    <?php if (has_term('promo', 'product_cat')) { ?>
                    <p class="precoparcelado" style="margin-bottom: -30px;"><span style="width: 20%;">À vista com
                            <?php echo $vdesconto ?>% de desconto:</span> <span>R$ <span id="valor-total3">0</span></span></p><br>
                    <?php }; ?>
                    <span id="parcela1">1 parcela de: R$ <span id="valor-1parcela">0</span> sem juros</span><br>
                    <span id="parcela2">2 parcelas de: R$ <span id="valor-2parcela">0</span> sem juros</span><br>
                    <span id="parcela3">3 parcelas de: R$ <span id="valor-3parcela">0</span> sem juros</span><br>
                    <span id="parcela4">4 parcelas de: R$ <span id="valor-4parcela">0</span> sem juros</span><br>
                    <span id="parcela5">5 parcelas de: R$ <span id="valor-5parcela">0</span> sem juros</span><br>
                    <?php if ($vjurostax > 0) { ?>
                        <span id="parcela6">Até 10 parc. de: R$ <span id="valor-10parcela">0</span> c/ juros de <?php echo $vjuros ?>% a.m. </span><br>
                    <?php } else { ?>
                        <span id="parcela6">10 parcelas de: R$ <span id="valor-10parcela">0</span> sem juros</span><br>
                    <?php } ?>
                </div>

            </div>
        </div>

        <?php do_action('woocommerce_after_add_to_cart_form'); ?>

        <?php if (current_user_can('editor') || current_user_can('administrator')) {
            global $wp;
            $current_url = home_url(add_query_arg(array(), $wp->request));
        ?>

            <a id="gerarlink">GERAR LINK PARA CLIENTE</a>

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $("#gerarlink").click(function() {
                        //var Tipocalibragem = $( "#top.single-product select.wccpf-field option:selected" ).val();
                        $('#linkgerado').html('<?php echo $current_url; ?>?q=' + quantidade);
                    });
                });
            </script>

            <p id="linkgerado"></p>

        <?php } ?>

    <?php endif; ?>
<?php // TERMINA O ELSEIF - FXM ?>

<?php // INICIA O ELSE - Produto Simples Sem Definição ?>
<?php } else { 
echo wc_get_stock_html($product); // WPCS: XSS ok.

    if ($product->is_in_stock()) :
         do_action('woocommerce_before_add_to_cart_form'); ?>
        <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
            <?php do_action('woocommerce_before_add_to_cart_button'); ?>
            <?php
            do_action('woocommerce_before_add_to_cart_quantity');

            woocommerce_quantity_input(array(
                'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
            ));
            do_action('woocommerce_after_add_to_cart_quantity');
            ?>
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
            <?php do_action('woocommerce_after_add_to_cart_button'); ?>

        </form>

        <?php do_action('woocommerce_after_add_to_cart_form');

    endif; ?>
<?php } ?>
<?php // TERMINA O ELSE - Produto Simples Sem Definição ?>