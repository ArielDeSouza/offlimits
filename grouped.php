<?php

/**
 * Grouped product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     4.8.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product, $post;

$parent_product_post = $post;

?>

<?php //adiciona o valor do desconto (sem o %) - linha 174 < ?php echo $vdesconto ? >
if (has_term('promo', 'product_cat')) { 
    $vdesconto  = 5;   //tem a categoria Promo, recebe o desconto
} else {
    $vdesconto  = 0;   //todos os demais
}

$vjuros     = 1;    //adiciona o valor dos juros da parcela - linha 175 < ?php echo $vjuros ? >
$vjurostax  = $vjuros / 100; //calcula a taxa de juros - linha 176 < ?php echo $vjurostax ? >
$vtotalparcela = 5; // Não configurado, lembrar de trocar o valor na linha 260
$vtxtjuros   = 'com juros';
?>

<?php if (has_term('configuravel', 'product_cat')) {
    $traseiroaberto = get_field('traseiro_aberto');
    $traseirofechado = get_field('traseiro_fechado');
    $traseirocurso = $traseiroaberto - $traseirofechado;

    $dianteiroaberto = get_field('dianteiro_aberto');
    $dianteirofechado = get_field('dianteiro_fechado');
    $dianteirocurso = $dianteiroaberto - $dianteirofechado;

    $dianteiromaximo = get_field('tamanho_maximo_dianteiro');
    $traseiromaximo = get_field('tamanho_maximo_traseiro');

    $dianteirominimo = get_field('tamanho_minimo_dianteiro');
    $traseirominimo = get_field('tamanho_minimo_traseiro');

    $dianteirofixacao = get_field('dianteirofixacao');
    $traseirofixacao = get_field('traseirofixacao');
} ?>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        var preco_global = '';
    })
</script>

<!-- Sumir com Inputs de Condições ( 15 -> FX5 | 80 -> FX6 | 77 -> FX7 ) -->

<style>
    #field_15_30,
    #field_15_38,
    #field_15_36,
    #field_15_31,
    #field_15_35,
    #field_15_34,
    #field_15_33,
    #field_15_32,
    #field_15_37,
    #field_80_24,
    #field_80_30,
    #field_80_38,
    #field_80_36,
    #field_80_31,
    #field_80_35,
    #field_80_34,
    #field_80_33,
    #field_80_32,
    #field_80_37,
    #field_83_30,
    #field_83_38,
    #field_83_36,
    #field_83_31,
    #field_83_35,
    #field_83_34,
    #field_83_33,
    #field_83_32,
    #field_83_37,
    #field_77_24,
    #field_77_30,
    #field_77_38,
    #field_77_36,
    #field_77_31,
    #field_77_35,
    #field_77_34,
    #field_77_33,
    #field_77_32,
    #field_77_37 {
        display: none !important;
    }
</style>

<?php // INICIA - Hook Antes do Adicionar ao Carrinho ?>
<?php do_action('woocommerce_before_add_to_cart_form'); ?>
<form class="cart grouped_form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>

    <?php

    $contagem = 0;

    foreach ($grouped_products as $grouped_product_child) :

        $product_id         = $grouped_product_child->get_id();
        $post_object        = get_post($grouped_product_child->get_id());
        $quantites_required = $quantites_required || ($grouped_product_child->is_purchasable() && !$grouped_product_child->has_options());
        $post               = $post_object; // WPCS: override ok.
        setup_postdata($post);

        $contagem++;

    ?>

        <div class="quantidades-<?php echo $product_id; ?> produtoindice<?php echo $contagem; ?>">
            <div class="765txt honey-size label" style="float: left;clear: both;min-width: 100px;">
                <h3 for="product-<?php echo $product_id; ?>" style="margin-top: 10px;margin-right: 15px;margin-bottom: 10px;font-weight: normal">
                    <?php
                    //função para mostrar o título
                    $the_title = get_the_title();
                    if (stripos($the_title, 'dianteiro')) {
                        echo 'DIANTEIRO';
                        $produto_titulo_dianteiro = $the_title;
                        $id_produto_dianteiro = $product_id;
                        $tipo = 'dianteiro';
                        $preco_dianteiro = get_post_meta($product_id, "wccaf_preo_dianteiro_smart", true);
                        ${'preco_smart_' . $product_id} = $preco_dianteiro;
                    } else if (stripos($the_title, 'traseiro')) {
                        echo 'TRASEIRO';
                        $produto_titulo_traseiro = $the_title;
                        $id_produto_traseiro = $product_id;
                        $tipo = 'traseiro';
                        $preco_traseiro = get_post_meta($product_id, "wccaf_preo_traseiro_smart", true);
                        ${'preco_smart_' . $product_id} = $preco_traseiro;
                    } else {
                        echo $the_title;
                    }
                    ?>
                </h3>
            </div>

            <div>
                <?php
                $quantites_required = true;

                woocommerce_quantity_input(
                    array(
                        'input_name'  => 'quantity[' . $product_id . ']',
                        'input_value' => (isset($_POST['quantity'][$product_id]) ? wc_stock_amount($_POST['quantity'][$product_id]) : 0),
                        'min_value'   => apply_filters('woocommerce_quantity_input_min', 0, $product),
                        'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product)
                    )
                );
                ?>
            </div>
        </div>
        <?php do_action('woocommerce_grouped_product_list_before_price', $product); ?>


        <?php
        /** mostra o preço do produto para o Javascript interpretar */
        $product_price = $grouped_product_child->get_price();
        // $product_price = $product->get_price();

        ?>

        <script type="text/javascript">
            var global_price = [];
            var total_price_<?php echo $product_id ?> = {};
            var valor = 0;
            var total = 0;
            var totalFinal = 0;
            var totalDesconto = 0;
            var totalParcela = 0;
            var QtyDianteiro = 0;
            var QtyTraseiro = 0;


            <?php if (has_term('configuravel', 'product_cat')) { ?>
                var extraDianteiro = 0;
                var extraTraseiro = 0;
            <?php } ?>
            var descontoavista = <?php echo $vdesconto ?>;
            var taxajuro = <?php echo $vjuros ?>;
            var jurostax = <?php echo $vjurostax ?>;
            var parcela1 = 0;
            var parcela2 = 0;
            var parcela3 = 0;
            var parcela4 = 0;
            var parcela5 = 0;
            var parcela10 = 0;
            var txtjuros = '<?php echo $vtxtjuros ?>';
            var temp = 0;
            var totalDianteiro = 0;
            var totalTraseiro = 0;
            jQuery(document).ready(function($) {

                $(document).on('change', '.wccpf-field[name="selecione_o_tipo_de_amortecedor"]', function() {
                    total_price_<?php echo $product_id ?> = {
                        valor: $('#preco-<?php echo $product_id ?>').val(),
                        id: <?php echo $product_id ?>
                    };
                    if ($('.wccpf-field[name="selecione_o_tipo_de_amortecedor"] option:selected').val() ===
                        'smart') {
                        valor = '<?php echo ${'preco_smart_' . $product_id} ?>';
                    } else {
                        valor = $('#preco-<?php echo $product_id ?>').val();
                    }
                    var quantidade = $('.qty-<?php echo $product_id ?>').val();
                    total_price_<?php echo $product_id ?>.valor = valor * quantidade;
                    <?php
                    if ($tipo == 'dianteiro') { ?>
                        QtyDianteiro = quantidade;
                        totalDianteiro = total_price_<?php echo $product_id ?>.valor;
                    <?php } else if ($tipo == 'traseiro') { ?>
                        QtyTraseiro = quantidade;
                        totalTraseiro = total_dianteiro = total_price_<?php echo $product_id ?>.valor;
                    <?php } ?>
                    console.log(valor);

                    <?php // se o array está vazio, preenche com o primeiro input 
                    ?>

                    if (global_price.length != 0) {
                        <?php //checa para ver se o objeto já está no array e substitui de acordo 
                        ?>
                        if (global_price[0].id == total_price_<?php echo $product_id ?>.id) {
                            global_price[0] = total_price_<?php echo $product_id ?>;
                        } else {
                            global_price[1] = total_price_<?php echo $product_id ?>;
                        }
                    } else {
                        global_price.push(total_price_<?php echo $product_id ?>);
                    }

                    for (i = 0; i < global_price.length; i++) {
                        temp += global_price[i].valor;
                    }

                    total = temp;
                    temp = 0;

                    totalFinal = total.toFixed(2).replace(".", ",");
                    totalTraseiro1 = totalTraseiro.toFixed(2).replace(".", ",");
                    totalDianteiro1 = totalDianteiro.toFixed(2).replace(".", ",");
                    totalDesconto = (total - (total * (descontoavista / 100))).toFixed(2).replace(".", ",");
                    totalParcela = (total / 5).toFixed(2).replace(".", ",");
                    
                    parcela1 = (total).toFixed(2).replace(".", ",");
                    parcela2 = (total / 2).toFixed(2).replace(".", ",");
                    parcela3 = (total / 3).toFixed(2).replace(".", ",");
                    parcela4 = (total / 4).toFixed(2).replace(".", ",");
                    parcela5 = (total / 5).toFixed(2).replace(".", ",");
                    parcela10 = ((total / 10) + (total * jurostax)).toFixed(2).replace(".", ",");
                    
                    $('#valor-total').html(totalFinal);
                    $('#valor-total2').html(totalFinal);
                    $('#valor-total3').html(totalDesconto);
                    $('#valor-desconto').html(totalDesconto);
                    $('#valor-pacela').html(totalParcela);
                    $('#valor-traseiro').html(totalTraseiro1);
                    $('#valor-dianteiro').html(totalDianteiro1);
                    $('#qty-traseiro').html(QtyTraseiro);
                    $('#qty-dianteiro').html(QtyDianteiro);


                    // FX5
                    //$('#input_15_30').val(<?= get_option('descontoavista') ?>);
                    $('#input_15_30').val(descontoavista);
                    $('#input_15_38').val(totalDesconto);
                    $('#input_15_36').val(parcela1);
                    $('#input_15_31').val(parcela2);
                    $('#input_15_35').val(parcela3);
                    $('#input_15_34').val(parcela4);
                    $('#input_15_33').val(parcela5);
                    $('#input_15_32').val(parcela10);
                    $('#input_15_37').val(txtjuros);

                    // FX6
                    $('#input_80_30').val(descontoavista);
                    $('#input_80_38').val(totalDesconto);
                    $('#input_80_36').val(parcela1);
                    $('#input_80_31').val(parcela2);
                    $('#input_80_35').val(parcela3);
                    $('#input_80_34').val(parcela4);
                    $('#input_80_33').val(parcela5);
                    $('#input_80_32').val(parcela10);
                    $('#input_80_37').val(txtjuros);

                    // FX7
                    $('#input_77_30').val(descontoavista);
                    $('#input_77_38').val(totalDesconto);
                    $('#input_77_36').val(parcela1);
                    $('#input_77_31').val(parcela2);
                    $('#input_77_35').val(parcela3);
                    $('#input_77_34').val(parcela4);
                    $('#input_77_33').val(parcela5);
                    $('#input_77_32').val(parcela10);
                    $('#input_77_37').val(txtjuros);
                    
                    // FX8
                    $('#input_83_30').val(descontoavista);
                    $('#input_83_38').val(totalDesconto);
                    $('#input_83_36').val(parcela1);
                    $('#input_83_31').val(parcela2);
                    $('#input_83_35').val(parcela3);
                    $('#input_83_34').val(parcela4);
                    $('#input_83_33').val(parcela5);
                    $('#input_83_32').val(parcela10);
                    $('#input_83_37').val(txtjuros);

                    $('#valor-1parcela').html(parcela1);
                    $('#valor-2parcela').html(parcela2);
                    $('#valor-3parcela').html(parcela3);
                    $('#valor-4parcela').html(parcela4);
                    $('#valor-5parcela').html(parcela5);
                    $('#valor-10parcela').html(parcela10);

                })


                $(document).on('click', '.quantidades-<?php echo $product_id ?> input[type="button"]', function() {
                    atualizaValores<?php echo $product_id ?>();
                });

                <?php if (has_term('configuravel', 'product_cat')) { ?>
                    jQuery('.mudatamanho').on('change', function() {
                        atualizaValores<?php echo $product_id ?>();
                        <?php //        var tamanhoadicional = ((jQuery('.tamanhotraseiro').val()) + (jQuery('.tamanhodianteiro').val()) );
                        //      jQuery("tamanhoadicional").val(tamanhoadicional); 
                        ?>
                    });
                <?php } ?>

                function atualizaValores<?php echo $product_id ?>() {

                    total_price_<?php echo $product_id ?> = {
                        valor: $('#preco-<?php echo $product_id ?>').val(),
                        id: <?php echo $product_id ?>
                    };
                    if ($('.wccpf-field[name="selecione_o_tipo_de_amortecedor"] option:selected').val() === 'smart') {
                        valor = '<?php echo ${'preco_smart_' . $product_id} ?>';
                    } else {
                        valor = $('#preco-<?php echo $product_id ?>').val();
                    }
                    var quantidade = $('.qty-<?php echo $product_id ?>').val();
                    total_price_<?php echo $product_id ?>.valor = valor * quantidade;
                    <?php
                    if ($tipo == 'dianteiro') { ?>
                        QtyDianteiro = quantidade;
                        totalDianteiro = total_price_<?php echo $product_id ?>.valor;
                    <?php } else if ($tipo == 'traseiro') { ?>
                        QtyTraseiro = quantidade;
                        totalTraseiro = total_dianteiro = total_price_<?php echo $product_id ?>.valor;
                    <?php } ?>

                    <?php // se o array está vazio, preenche com o primeiro input
                    ?>
                    if (global_price.length != 0) {
                        <?php //checa para ver se o objeto já está no array e substitui de acordo 
                        ?>
                        if (global_price[0].id == total_price_<?php echo $product_id ?>.id) {
                            global_price[0] = total_price_<?php echo $product_id ?>;
                        } else {
                            global_price[1] = total_price_<?php echo $product_id ?>;
                        }
                    } else {
                        global_price.push(total_price_<?php echo $product_id ?>);
                    }

                    for (i = 0; i < global_price.length; i++) {
                        temp += global_price[i].valor;
                    }
                    <?php if (has_term('configuravel', 'product_cat')) { ?>
                        extraDianteiro = (Number(jQuery('.tamanhodianteiro').val())) * (25) * (QtyDianteiro);
                        extraTraseiro = (Number(jQuery('.tamanhotraseiro').val())) * (25) * (QtyTraseiro);
                        console.log('ta acionado');
                    <?php } ?>


                    var traseiroaberto = Number(<?php echo $traseiroaberto ?> + (((Number(jQuery('.tamanhotraseiro')
                        .val()))) * 25.4)).toFixed(0);
                    var traseirofechado = Number(<?php echo $traseirofechado ?> + (((Number(jQuery('.tamanhotraseiro')
                        .val()))) * 25.4).toFixed(0) / 2).toFixed(0);
                    var traseirocurso = (traseiroaberto - traseirofechado).toFixed(0);

                    jQuery(".traseiroaberto").html(traseiroaberto + ' mm');
                    jQuery(".traseirofechado").html(traseirofechado + ' mm');
                    jQuery(".traseirocurso").html(traseirocurso + ' mm');

                    var dianteiroaberto = Number(<?php echo $dianteiroaberto ?> + (((Number(jQuery('.tamanhodianteiro')
                        .val()))) * 25.4)).toFixed(0);
                    var dianteirofechado = Number(<?php echo $dianteirofechado ?> + (((Number(jQuery(
                        '.tamanhodianteiro').val()))) * 25.4).toFixed(0) / 2).toFixed(0);
                    var dianteirocurso = (dianteiroaberto - dianteirofechado).toFixed(0);

                    jQuery(".dianteiroaberto").html(dianteiroaberto + ' mm');
                    jQuery(".dianteirofechado").html(dianteirofechado + ' mm');
                    jQuery(".dianteirocurso").html(dianteirocurso + ' mm');


                    total = ((temp) <?php if (has_term('configuravel', 'product_cat')) { ?> + extraDianteiro +
                            extraTraseiro<?php } ?>);
                    temp = 0;


                    totalFinal = (total).toFixed(2).replace(".", ",");
                    totalTraseiro1 = (totalTraseiro<?php if (has_term('configuravel', 'product_cat')) { ?> +
                        extraTraseiro<?php } ?>).toFixed(2).replace(".", ",");
                    totalDianteiro1 = (totalDianteiro<?php if (has_term('configuravel', 'product_cat')) { ?> +
                        extraDianteiro<?php } ?>).toFixed(2).replace(".", ",");
                    totalDesconto = (total - (total * (descontoavista / 100))).toFixed(2).replace(".", ",");


                    <?php if ($vjurostax > 0) { ?>
                        totalParcela = (total / 5).toFixed(2).replace(".", ",");
                        parcela1 = (total).toFixed(2).replace(".", ",");
                        parcela2 = (total / 2).toFixed(2).replace(".", ",");
                        parcela3 = (total / 3).toFixed(2).replace(".", ",");
                        parcela4 = (total / 4).toFixed(2).replace(".", ",");
                        parcela5 = (total / 5).toFixed(2).replace(".", ",");
                        parcela10 = ((total / 10) + (total * jurostax)).toFixed(2).replace(".", ",");
                    <?php } else { ?>
                        totalParcela = (total / 10).toFixed(2).replace(".", ",");
                        parcela1 = (total).toFixed(2).replace(".", ",");
                        parcela2 = (total / 2).toFixed(2).replace(".", ",");
                        parcela3 = (total / 3).toFixed(2).replace(".", ",");
                        parcela4 = (total / 4).toFixed(2).replace(".", ",");
                        parcela5 = (total / 5).toFixed(2).replace(".", ",");
                        parcela10 = (total / 10).toFixed(2).replace(".", ",");
                    <?php } ?>

                    $('#valor-total').html(totalFinal);
                    $('#valor-total2').html(totalFinal);
                    $('#valor-total3').html(totalDesconto);
                    $('#valor-desconto').html(totalDesconto);
                    $('#valor-pacela').html(totalParcela);
                    $('#valor-traseiro').html(totalTraseiro1);
                    $('#valor-dianteiro').html(totalDianteiro1);
                    $('#qty-traseiro').html(QtyTraseiro);
                    $('#qty-dianteiro').html(QtyDianteiro);
                    $('#valor-1parcela').html(parcela1);
                    $('#valor-2parcela').html(parcela2);
                    $('#valor-3parcela').html(parcela3);
                    $('#valor-4parcela').html(parcela4);
                    $('#valor-5parcela').html(parcela5);
                    $('#valor-10parcela').html(parcela10);

                    // FX5
                    $('#input_15_30').val(descontoavista);
                    $('#input_15_38').val(totalDesconto);
                    $('#input_15_36').val(parcela1);
                    $('#input_15_31').val(parcela2);
                    $('#input_15_35').val(parcela3);
                    $('#input_15_34').val(parcela4);
                    $('#input_15_33').val(parcela5);
                    $('#input_15_32').val(parcela10);
                    $('#input_15_37').val(txtjuros);

                    // FX6
                    $('#input_80_30').val(descontoavista);
                    $('#input_80_38').val(totalDesconto);
                    $('#input_80_36').val(parcela1);
                    $('#input_80_31').val(parcela2);
                    $('#input_80_35').val(parcela3);
                    $('#input_80_34').val(parcela4);
                    $('#input_80_33').val(parcela5);
                    $('#input_80_32').val(parcela10);
                    $('#input_80_37').val(txtjuros);

                    // FX7
                    $('#input_77_30').val(descontoavista);
                    $('#input_77_38').val(totalDesconto);
                    $('#input_77_36').val(parcela1);
                    $('#input_77_31').val(parcela2);
                    $('#input_77_35').val(parcela3);
                    $('#input_77_34').val(parcela4);
                    $('#input_77_33').val(parcela5);
                    $('#input_77_32').val(parcela10);
                    $('#input_77_37').val(txtjuros);

                    // FX8
                    $('#input_83_30').val(descontoavista);
                    $('#input_83_38').val(totalDesconto);
                    $('#input_83_36').val(parcela1);
                    $('#input_83_31').val(parcela2);
                    $('#input_83_35').val(parcela3);
                    $('#input_83_34').val(parcela4);
                    $('#input_83_33').val(parcela5);
                    $('#input_83_32').val(parcela10);
                    $('#input_83_37').val(txtjuros);
                }

            });
        </script>

        <input id="preco-<?php echo $product_id ?>" class="preco_input" style="visibility: hidden; display: none" value="<?php echo $product_price; ?>">
        <input id="preco-smart-<?php echo $product_id ?>" class="preco_smart_input" style="visibility: hidden; display: none" value="<?php echo ${'preco_smart_' . $product_id}; ?>">

        <?php if ($tipo == 'dianteiro') { ?>
            <input id="id_product_dianteiro" value="<?php echo $id_produto_dianteiro ?>" style="visibility: hidden; display: none">
            <input class="titulo_produto_dianteiro" value="<?php echo $produto_titulo_dianteiro ?>" style="visibility: hidden; display: none">
        <?php } else if ($tipo == 'traseiro') { ?>
            <input id="id_product_traseiro" value="<?php echo $id_produto_traseiro ?>" style="visibility: hidden; display: none">
            <input class="titulo_produto_traseiro" value="<?php echo $produto_titulo_traseiro ?>" style="visibility: hidden; display: none">
        <?php
        }

        if ($availability = $grouped_product_child->get_availability()) {
            $availability_html = empty($availability['availability']) ? '' : '<p class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>';
            echo apply_filters('woocommerce_stock_html', $availability_html, $availability['availability'], $grouped_product_child);
        }
        ?>

    <?php
    endforeach;

    // Reset to parent grouped product
    $post    = $parent_product_post;
    $product = wc_get_product($parent_product_post->ID);
    setup_postdata($parent_product_post);
    ?>

    <br>

    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->id); ?>" />

    <?php if ($quantites_required) : ?>

        <?php do_action('woocommerce_before_add_to_cart_button'); ?>


        <div class="valorescompra honey-size" style="display:none; margin: 20px 0 0;padding: 5px;">
            <h2>ORÇAMENTO:</h2>
        </div>
        <div class="valorescompra" style="display:none; margin: -10px 0 30px; padding: 5px;background: #fff;">


            <div class="honey-size">

                <p class="precoparcelado"><span style="width: 20%;">Dianteiro:</span> <span>R$ <span id="valor-dianteiro">0</span> (<span id="qty-dianteiro">0</span> unidades)</span></p>
                <p class="precoparcelado"><span style="width: 20%;">Traseiro:</span> <span>R$ <span id="valor-traseiro">0</span> (<span id="qty-traseiro">0</span> unidades)</span></p>
                <p class="precoparcelado"><span style="width: 20%;">TOTAL:</span> <span>R$ <span id="valor-total">0</span></span>
                    <?php if (get_option('fretegratis')) {
                        echo '<b> (FRETE GRÁTIS)</b>';
                    }; ?>
                </p>

            </div>

            <div>

                <?php if (has_term('promo', 'product_cat')) { ?>
                <div class="honey-size colunapreco1">
                    <p class="colunaprecocondicao">R$ <span id="valor-desconto">0</span> à vista</p>
                    <p class="colunaprecocondicaoobs">(<?php echo $vdesconto; ?>% de desconto)</p>
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

    <?php endif; ?>
</form>
<?php // Termina - Hook Antes do Adicionar ao Carrinho ?>
<?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php // INICIA - Script Site Blindado ?>
<div id="armored_website" class="blindadoproduto" style="margin-top:5px;  margin-bototm:15px;">
    <param id="aw_preload" value="true" />
    <param id="aw_use_cdn" value="true" />
</div>
<script type="text/javascript" src="//cdn.siteblindado.com/aw.js"></script>
<?php // TERMINA - Script Site Blindado ?>


<?php // INICIA O IF - FX5 ?>
<?php if (has_term('FX5', 'product_cat')) { ?>

    <div class="above_cart_button" style="margin-top: 23px; font-style: initial; width: 100%; text-align: left; font-size: 13px; display: none;">
        ENVIAMOS O ORÇAMENTO PARA SEU EMAIL. <?php if (get_option('fretegratis')) {
            echo '<b> (FRETE GRÁTIS)</b>';
        } else {
            echo 'O FRETE SERÁ CALCULADO NA PRÓXIMA TELA.';
        } ?>
    </div>
    <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
        <div class="avia_textblock area-form-produto" itemprop="text">
            <?php echo do_shortcode('[gravityform id=15 title=false ajax=true]'); ?>
        </div>
    </section>
<?php } ?>
<?php // TERMINA O IF - FX5 ?>


<?php // INICIA O IF - FX6 ?>
<?php if (has_term('FX6', 'product_cat')) { ?>
    <div class="above_cart_button" style="margin-top: 23px; font-style: initial; width: 100%; text-align: left; font-size: 13px; display: none;">
        ENVIAMOS O ORÇAMENTO PARA SEU EMAIL. <?php if (get_option('fretegratis')) {
            echo '<b> (FRETE GRÁTIS)</b>';
        } else {
            echo 'O FRETE SERÁ CALCULADO NA PRÓXIMA TELA.';
        } ?>
    </div>
    <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
        <div class="avia_textblock area-form-produto" itemprop="text">
            <?php echo do_shortcode('[gravityform id=80 title=false ajax=true]'); ?>
        </div>
    </section>
<?php } ?>
<?php // TERMINA O IF - FX6 ?>


<?php // INICIA O IF - FX7 ?>
<?php if (has_term('FX7', 'product_cat')) { ?>
    <div class="above_cart_button" style="margin-top: 23px; font-style: initial; width: 100%; text-align: left; font-size: 13px; display: none;">
        ENVIAMOS O ORÇAMENTO PARA SEU EMAIL. <?php if (get_option('fretegratis')) {
            echo '<b> (FRETE GRÁTIS)</b>';
        } else {
            echo 'O FRETE SERÁ CALCULADO NA PRÓXIMA TELA.';
        } ?>
    </div>
    <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
        <div class="avia_textblock area-form-produto" itemprop="text">
            <?php echo do_shortcode('[gravityform id=77 title=false ajax=true]'); ?>
        </div>
    </section>
<?php } ?>
<?php // TERMINA O IF - FX7 ?>


<?php // INICIA O IF - FX8 ?>
<?php if (has_term('FX8', 'product_cat')) { ?>
    <div class="above_cart_button" style="margin-top: 23px; font-style: initial; width: 100%; text-align: left; font-size: 13px; display: none;">
        ENVIAMOS O ORÇAMENTO PARA SEU EMAIL. <?php if (get_option('fretegratis')) {
            echo '<b> (FRETE GRÁTIS)</b>';
        } else {
            echo 'O FRETE SERÁ CALCULADO NA PRÓXIMA TELA.';
        } ?>
    </div>
    <section class="av_textblock_section" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
        <div class="avia_textblock area-form-produto" itemprop="text">
            <?php echo do_shortcode('[gravityform id=83 title=false ajax=true]'); ?>
        </div>
    </section>
<?php } ?>
<?php // TERMINA O IF - FX8 ?>



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

            #field_15_24 {
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
                <span id="parcela6">Até 10 parc. de: R$ <span id="valor-10parcela">0</span> c/ juros de <?php echo $vjuros ?>% a.m.. </span><br>
            <?php } else { ?>
                <span id="parcela6">10 parcelas de: R$ <span id="valor-10parcela">0</span> sem juros</span><br>
            <?php } ?>
        </div>

    </div>
</div>

<?php if (current_user_can('editor') || current_user_can('administrator')) {
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
?>
    <a id="gerarlink">GERAR LINK PARA CLIENTE</a>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("#gerarlink").click(function() {

                //var Tipocalibragem = $( "select.tipocalibragem option:selected" ).val();
                <?php if (has_term('configuravel', 'product_cat')) { ?>
                    var Tdianteiro = $("select.tamanhodianteiro option:selected").val();
                    var Ttraseiro = $("select.tamanhotraseiro option:selected").val();
                <?php } ?>
                $('#linkgerado').html('<?php echo $current_url; ?>?t=' + QtyTraseiro + '&d=' + QtyDianteiro +
                    <?php if (has_term('configuravel', 'product_cat')) { ?> '&tt=' + Ttraseiro + '&td=' +
                        Tdianteiro + <?php } ?> '');
            });
        });
    </script>
    <p id="linkgerado"></p>
<?php } ?>
<?php if (has_term('configuravel', 'product_cat')) { ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tabelaproduto1">
        <tbody>
            <tr>
                <td>ABERTO</td>
                <td>FECHADO</td>
                <td>CURSO</td>
                <td>FIXAÇÃO</td>
            </tr>
            <tr>
                <td class="traseiroaberto"><?php echo $traseiroaberto ?> mm</td>
                <td class="traseirofechado"><?php echo $traseirofechado ?> mm</td>
                <td class="traseirocurso"><?php echo $traseirocurso ?> mm</td>
                <td class="traseirofixacao"><?php echo $traseirofixacao ?></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tabelaproduto2">
        <tbody>
            <tr>
                <td>ABERTO</td>
                <td>FECHADO</td>
                <td>CURSO</td>
                <td>FIXAÇÃO</td>
            </tr>
            <tr>
                <td class="dianteiroaberto"><?php echo $dianteiroaberto ?> mm</td>
                <td class="dianteirofechado"><?php echo $dianteirofechado ?> mm</td>
                <td class="dianteirocurso"><?php echo $dianteirocurso ?> mm</td>
                <td class="dianteirofixacao"><?php echo $dianteirofixacao ?></td>
            </tr>
        </tbody>
    </table>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(".produtoindice2").append(jQuery('select.tamanhotraseiro'));
            $(".produtoindice2").append(jQuery('#tabelaproduto1'));
            $(".produtoindice1").append(jQuery('select.tamanhodianteiro'));
            $(".produtoindice1").append(jQuery('#tabelaproduto2'));
            $(".mudatamanho-wrapper").remove();


            $('select.tamanhotraseiro option').filter(function(index) {
                return index > 1 && index > <?php echo $traseiromaximo; ?>;
            }).remove();

            $('select.tamanhodianteiro option').filter(function(index) {
                return index > 1 && index > <?php echo $dianteiromaximo; ?>;
            }).remove();

            $('select.tamanhotraseiro option').filter(function(index) {
                return index > 1 && index < <?php echo $traseirominimo; ?>;
            }).remove();

            $('select.tamanhodianteiro option').filter(function(index) {
                return index > 1 && index < <?php echo $dianteirominimo; ?>;
            }).remove();


        });
    </script>
    <style type="text/css">
        body#top.single-product select.mudatamanho {
            width: 170px !important;
            margin: 9px 0 0 !important;

            <?php if (current_user_can('editor') || current_user_can('administrator') || current_user_can('vendedor') || current_user_can('aplicador')) {
            ?><?php
            } else {
            ?>opacity: 0.5;
            pointer-events: none !important;
            background-image: none !important;
            text-transform: uppercase;
            background: none !important;
            font-size: 12px !important;
            border: none !important;
            <?php
            }

            ?>
        }

        table#tabelaproduto1,
        table#tabelaproduto2 {
            margin-bottom: 40px;
            max-width: 417px;
        }
    </style>
<?php } ?>