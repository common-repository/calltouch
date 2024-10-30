<?php
/*
Plugin Name:  Calltouch
Plugin URI:   https://www.calltouch.ru
Description:  Интеграция с Calltouch позволит вам эффективно анализировать отдачу ваших рекламных кампаний: вы узнаете сколько тратите на рекламу, сколько эта реклама приносит вам звонков и заявок, и главное, сколько эти лиды приносят вам реальной выручки.
Version:      1.3.3
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/
function calltouch_get_settings_title()
{
    return 'Подключение к Calltouch';
}

function calltouch_init_javascript()
{
    $modIds = get_option('calltouch_mod_id');
    if (!$modIds) {
        return;
    }
    if (!is_array($modIds)) {
        $modIds = [$modIds];
    }
    $modIds = array_map(function ($id) {
        return esc_js($id);
    }, $modIds);

    if (count($modIds) === 1) {
        $modId = $modIds[0];
        ?>
        <!-- calltouch -->
        <script type="text/javascript">
            (function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n]["callbacks"].push(arguments)};if(!w[n]["callbacks"]){w[n]["callbacks"]=[]}w[n]["loaded"]=false;if(typeof c!=="object"){c=[c]}w[n]["counters"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName("script")[0],s=d.createElement("script"),i=function(){a.parentNode.insertBefore(s,a)},m=typeof Array.prototype.find === 'function',n=m?"init-min.js":"init.js";s.type="text/javascript";s.async=true;s.src="https://mod.calltouch.ru/"+n+"?id="+cId;if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",i,false)}else{i()}}})(window,document,"ct","<?php echo $modId;?>");
        </script>
        <!-- calltouch -->
        <?php
    } else {
        $modStr = '';
        foreach ($modIds as $modId) {
            $modStr .= ',"' . $modId .'"';
        }
        $modIds = trim($modStr, ',');
        ?>
        <!-- calltouch -->
        <script type="text/javascript">
            (function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n]["callbacks"].push(arguments)};
if(!w[n]["callbacks"]){w[n]["callbacks"]=[]}w[n]["loaded"]=false;
if(typeof c!=="object"){c=[c]}w[n]["counters"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}
function p(cId){var a=d.getElementsByTagName("script")[0],
s=d.createElement("script"),i=function(){a.parentNode.insertBefore(s,a)};
s.type="text/javascript";s.async=true;s.src="https://mod.calltouch.ru/init.js?id="+cId;
if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",i,false)}else{i()}}
})(window,document,"ct",[<?php echo $modIds;?>]);
        </script>
        <!-- calltouch -->
        <?php
    }

}

function calltouch_admin_menu_settings()
{
    if (isset($_POST['change'])) {
        $modIds = [];
        if (isset($_POST['mod_id']) && is_array($_POST['mod_id'])) {
            foreach ($_POST['mod_id'] as $modId) {
                $modIds[] = sanitize_key($modId);
            }
        }
        if (!$modIds) {
            delete_option('calltouch_mod_id');
        } else {
                update_option('calltouch_mod_id', $modIds);
        }
    } else {
        $modIds = [];
        if (get_option('calltouch_mod_id')) {
            $modIds = is_array(get_option('calltouch_mod_id')) ? get_option('calltouch_mod_id') : [get_option('calltouch_mod_id')];
        }
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(calltouch_get_settings_title()); ?></h1>
        <p>
            Интеграция с Calltouch позволит вам эффективно анализировать отдачу ваших рекламных кампаний: вы узнаете
            сколько тратите на рекламу, сколько эта реклама приносит вам звонков и заявок, и главное, сколько эти лиды
            приносят вам реальной выручки.
        </p>
        <p>
            Для подключения интеграции вам необходимо нажать на кнопку <strong>«Добавить ID счетчика»</strong>, после чего ввести идентификатор счетчика из личного кабинета Calltouch и нажать кнопку <strong>«Сохранить»</strong> – скрипт будет установлен на все страницы вашего сайта.
        </p>
        <p>
            Если ваш сайт отслеживается сразу в нескольких личных кабинетах Calltouch, то вам не обходимо добавить нужно количество полей, нажав на кнопку <strong>«Добавить ID счетчика»</strong> и ввести идентификаторы каждого счетчика каждого кабинета Calltouch.
        </p>
        <p>
            При удалении поля <strong>«ID счетчика Calltouch»</strong> и сохранении, идентификатор удаляется из скрипта. Если удалить все <strong>«ID счетчика Calltouch»</strong> и сохранить, то скрипт будет удалён из кода вашего сайта.
        </p>
        <a type="submit" id="calltouch-add-mod-id" class="button button-primary">Добавить ID счетчика</a>
        <br>
        <br>
        <form id="calltouch-mod-id-form" method="post" novalidate="novalidate">
            <input type="hidden" name="change" value="1">
            <table class="form-table" role="presentation">
                <tbody id="calltouch-mod-id-container">
                <?php
                $num = 0;
                foreach ($modIds as $modId) {
                    $num++;
                ?>
                <tr>
                    <th scope="row"><label for="calltouch-mod-id-<?php echo esc_attr($num); ?>">ID счетчика Calltouch #<span class="calltouch-mod-id-num"><?php echo esc_attr($num); ?></span></label></th>
                    <td><input class="calltouch-mod-id" maxlength="8" id="calltouch-mod-id-<?php echo esc_attr($num); ?>" name="mod_id[]" type="text"
                               value="<?php echo esc_attr($modId); ?>"/> <a class="button button-primary calltouch-del-mod-id">удалить</a>
                        <div style="color: red" class="calltouch-mod-error"></div></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>

            <input type="submit" name="submit" id="calltouch-save-mod-id" class="button button-primary"
                   value="Сохранить"></p>
        </form>
        <p>У вас еще нет личного кабинета Calltouch? <a
                    href="https://www.calltouch.ru/why-calltouch/?utm_source=wordpress&utm_medium=cms&utm_campaign=wordpress_app&utm_content=script_app">Оставьте
                заявку</a> на подключение.</p>
    </div>

    <script>
        document.addEventListener('click',function(e){
            if(e.target && e.target.classList.contains('calltouch-del-mod-id')){
                e.target.parentNode.parentNode.parentNode.removeChild(e.target.parentNode.parentNode);
                calltouch_update_mod_id_num();
                e.stopPropagation();
            }
        });

        document.getElementById('calltouch-mod-id-form').onsubmit = function (event) {
            var inputs = document.getElementsByClassName("calltouch-mod-id");

            var usedValues = [];
            var hasError = false;
            Array.prototype.forEach.call(inputs, function(input, num) {
                var errDiv = input.parentNode.getElementsByClassName("calltouch-mod-error").item(0)
                errDiv.innerHTML = '';
                var err;
                if (input.value.length < 8) {
                    err = document.createElement('div')
                    err.innerHTML = 'Слишком короткое значение'
                    errDiv.append(err)
                    hasError = true;
                } else if (!( new RegExp('^[0-9a-z]+$')).test(input.value)) {
                    err = document.createElement('div')
                    err.innerHTML = 'Некорректное значение'
                    errDiv.append(err)
                    hasError = true;
                } else if (usedValues.indexOf(input.value) !== -1) {
                    err = document.createElement('div')
                    err.innerHTML = 'Такое значение уже указано'
                    errDiv.append(err)
                    hasError = true;
                }
                usedValues.push(input.value);
            });
            return !hasError;
        }
        function calltouch_update_mod_id_num()
        {
            var inputs = document.getElementsByClassName("calltouch-mod-id");
            Array.prototype.forEach.call(inputs, function(input, num) {
                var numContainer = input.parentNode.parentNode.getElementsByClassName("calltouch-mod-id-num").item(0)
                numContainer.innerHTML = num+1;
            });
        }

        document.getElementById('calltouch-add-mod-id').onclick = function (event) {
            var modIdCount = document.getElementsByClassName("calltouch-mod-id").length


            var tr = document.createElement('tr');
            tr.innerHTML =
                '<th scope="row"><label for="calltouch-mod-id-'+(modIdCount+1)+'">ID счетчика Calltouch #<span class="calltouch-mod-id-num">'+(modIdCount+1)+'</span></label></th>' +
                '<td><input  class="calltouch-mod-id" maxlength="8"  id="calltouch-mod-id-'+(modIdCount+1)+'" name="mod_id[]" type="text" value=""/>' +
                '  <a id="calltouch-del-mod-id" class="button button-primary calltouch-del-mod-id">удалить</a>' +
                '<div style="color: red" class="calltouch-mod-error"></div>' +
                '</td>'
            ;
            document.getElementById('calltouch-mod-id-container').append(tr);
            calltouch_update_mod_id_num();
            event.stopPropagation();
        }
    </script>
    <?php
}

function calltouch_init_admin_menu()
{
    add_menu_page(
        'Calltouch',
        calltouch_get_settings_title(),
        'edit_plugins',
        'calltouch_menu_settings',
        'calltouch_admin_menu_settings',
        plugins_url('favicon-20x20.ico', __FILE__)
    );
}
;
add_action('wp_footer', 'calltouch_init_javascript', 512);
add_action('admin_menu', 'calltouch_init_admin_menu');
