<?php

return [
    'ad'				        => 'Active Directory',
    'ad_domain'				    => 'Домен Active directory',
    'ad_domain_help'			=> 'Іноді це те саме, що і ваш домен електронної пошти, але не завжди.',
    'ad_append_domain_label'    => 'Додати доменне ім\'я',
    'ad_append_domain'          => 'Додати доменне ім\'я для поля користувача',
    'ad_append_domain_help'     => 'Користувач не потрібний для запису "username@domain.local", вони можуть просто ввести "ім\'я користувача".',
    'admin_cc_email'            => 'Адреса електронної пошти CC',
    'admin_cc_email_help'       => 'Якщо ви бажаєте відправити копію листів, що відправляються користувачам, до додаткового облікового запису, введіть його тут. В іншому випадку залиште це поле порожнім.',
    'admin_settings'            => 'Адміністративні налаштування',
    'is_ad'				        => 'Це сервер Active Directory',
    'alerts'                	=> 'Попередження',
    'alert_title'               => 'Оновити налаштування сповіщень',
    'alert_email'				=> 'Надіслати сповіщення',
    'alert_email_help'    => 'Адреси електронної пошти або списки розсилки, вам потрібно, щоб будуть відправлятися попередження, розділені комами',
    'alerts_enabled'			=> 'Увімкнути сповіщення по електронній пошті',
    'alert_interval'			=> 'Поріг Закінчення сповіщень (в днях)',
    'alert_inv_threshold'		=> 'Поріг сповіщення про інвентар',
    'allow_user_skin'           => 'Дозволити користувацький скін',
    'allow_user_skin_help_text' => 'Вибір цього параметра дозволить користувачеві перевизначити стиль інтерфейсу з іншим.',
    'asset_ids'					=> 'ID активів',
    'audit_interval'            => 'Інтервал аудиту',
    'audit_interval_help'       => 'Якщо вам потрібно регулярно перевіряти активи, введіть інтервал у місяці, що ви використовуєте. Якщо ви оновите це значення, всі "наступні терміни" для активів з майбутньою датою аудиту будуть оновлені.',
    'audit_warning_days'        => 'Поріг попередження аудиту',
    'audit_warning_days_help'   => 'Скільки днів заздалегідь слід попередити, коли активи мають бути призначені для перевірки?',
    'auto_increment_assets'		=> 'Створення автоматичного збільшення тегів медіафайлів',
    'auto_increment_prefix'		=> 'Префікс (необов\'язково)',
    'auto_incrementing_help'    => 'Спочатку увімкніть автоматичне збільшення міток медіафайлів',
    'backups'					=> 'Резервні копії',
    'backups_help'              => 'Створення, завантаження і відновлення резервних копій ',
    'backups_restoring'         => 'Відновлення з резервної копії',
    'backups_upload'            => 'Завантажити резервну копію',
    'backups_path'              => 'Резервні копії на сервері зберігаються в <code>:path</code>',
    'backups_restore_warning'   => 'Скористайтеся кнопкою відновлення <small><span class="btn btn-xs btn-warning"><i class="text-white fas fa-retweet" aria-hidden="true"></i></span> </small> для відновлення з попередньої резервної копії. (Наразі це не працює зі сховищем файлів S3 або Docker.)<br><br>Ваша <strong>вся база даних :app_name і всі завантажені файли будуть повністю замінені</strong> файлом резервної копії.  ',
    'backups_logged_out'         => 'Усі наявні користувачі, включаючи вас, вийдуть з системи після завершення відновлення.',
    'backups_large'             => 'Дуже великі резервні копії можуть вийти за допомогою командного рядка. ',
    'barcode_settings'			=> 'Параметри штрих-кодів',
    'confirm_purge'			    => 'Очистити',
    'confirm_purge_help'		=> 'Введіть текст "ВИДАЛЕННЯ" у полі знизу, щоб очистити ваші видалені записи. Цю дію не можна буде скасувати, після чого буде застосовано PERMANENTLY видаліть усі раніше видалені вами елементи. (Спочатку ви повинні зробити резервну копію, лише щоб бути безпечним.)',
    'custom_css'				=> 'Власний CSS',
    'custom_css_help'			=> 'Введіть будь-які власні зміни CSS, які ви хотіли б використовувати. Не включайте &lt;style&gt;&lt;/style&gt;.',
    'custom_forgot_pass_url'	=> 'URL для скидання пароля',
    'custom_forgot_pass_url_help'	=> 'Це замінює вбудовану URL-адресу пароля на екрані авторизації - направляти людей на внутрішні чи хостингові скидання пароля LDAP функціональності. Уникне вимкнення функціоналу пароля для імені локального користувача.',
    'dashboard_message'			=> 'Повідомлення в меню налаштувань',
    'dashboard_message_help'	=> 'Цей текст з\'явиться на панелі керування для будь-кого, хто має дозвіл на перегляд панелі керування.',
    'default_currency'  		=> 'Валюта за замовчуванням',
    'default_eula_text'			=> 'EULA за замовчуванням',
    'default_language'			=> 'Мова за замовчуванням',
    'default_eula_help_text'	=> 'Ви також можете пов\'язати користувальницькі EULA з конкретними категоріями активів.',
    'acceptance_note'           => 'Add a note for your decision (Optional)',
    'display_asset_name'        => 'Відображення назви активу',
    'display_checkout_date'     => 'Показувати дату оформлення замовлення',
    'display_eol'               => 'Відображати EOL у вигляді таблиці',
    'display_qr'                => 'Показати квадрати коди',
    'display_alt_barcode'		=> 'Відображати 1D штрих-код',
    'email_logo'                => 'Логотип електронної пошти',
    'barcode_type'				=> '2D тип коду',
    'alt_barcode_type'			=> '1D тип штрихкоду',
    'email_logo_size'       => 'Квадратні логотипи в електронному листі виглядають найкращими. ',
    'enabled'                   => 'Увімкнено',
    'eula_settings'				=> 'Налаштування EULA',
    'eula_markdown'				=> 'Цей EULA дозволяє <a href="https://help.github.com/articles/github-flavored-markdown/">Github розміщений на markdown</a>.',
    'favicon'                   => 'Іконка',
    'favicon_format'            => 'Допустимі типи файлів - це значок, png та gif. Інші формати зображень можуть не працювати у всіх браузерах.',
    'favicon_size'          => 'Значки вподобань мають бути квадратними зображеннями, 16х16 пікселів.',
    'footer_text'               => 'Додатковий текст у футері ',
    'footer_text_help'          => 'Цей текст з\'явиться у правому нижньому колонтитулі. Посилання дозволені використовувати <a href="https://help.github.com/articles/github-flavored-markdown/">Github flavored markdown</a>. Розриви ліній, заголовки, зображення і т. д. можуть призвести до непередбачуваних результатів.',
    'general_settings'			=> 'Загальні налаштування',
    'general_settings_keywords' => 'компанія підтримує, підпис, прийнятання, формат електронної пошти, формату, картинки, на одну сторінку, thumbnail, eula, gravatar, tos, dashboard, privacy',
    'general_settings_help'     => 'EULA за замовчуванням',
    'generate_backup'			=> 'Створити резервну копію',
    'google_workspaces'         => 'Робочі області Google',
    'header_color'              => 'Колір заголовку',
    'info'                      => 'Ці параметри дозволяють налаштувати деякі аспекти інсталяції.',
    'label_logo'                => 'Логотип для Міток',
    'label_logo_size'           => 'Квадратний логотип виглядає найкраще - відображатиметься у верхньому правому куті кожної позначки активів. ',
    'laravel'                   => 'Версія Laravel',
    'ldap'                      => 'LDAP',
    'ldap_default_group'        => 'Типова група прав',
    'ldap_default_group_info'   => 'Виберіть групу, щоб призначити новим синхронізованим користувачам. Пам\'ятайте, що користувач бере дозволи цієї групи.',
    'no_default_group'          => 'Без типової групи',
    'ldap_help'                 => 'LDAP/Активний каталог',
    'ldap_client_tls_key'       => 'LDAP-клієнт TLS ключ',
    'ldap_client_tls_cert'      => 'LDAP сертифікат TLS від клієнта',
    'ldap_enabled'              => 'LDAP включено',
    'ldap_integration'          => 'Інтеграція з LDAP',
    'ldap_settings'             => 'Параметри LDAP',
    'ldap_client_tls_cert_help' => 'Сертифікат Client-Side TLS та ключ для з\'єднань LDAP, як правило, корисні лише в конфігураціях Google Workspace з "Secure LDAP". Обидва є обов\'язковими.',
    'ldap_location'             => 'LDAP розташування',
'ldap_location_help'             => 'Поле LDAP Location повинно використовуватися, якщо <strong>OU не вказано у Base Bind DN.</strong> Залиште поле порожнім, якщо потрібно використовувати пошук OU.',
    'ldap_login_test_help'      => 'Введіть дійсне ім\'я користувача і пароль LDAP з базового DN, який ви вказали, щоб перевірити чи правильно налаштований ваш логін LDAP. Ви ПОВИННІ ЗБЕРЕГТИ ВАШ ВИДАЛЕНО ДОДАТНО ДОСТУПНІ ПЕРШЕНЬ.',
    'ldap_login_sync_help'      => 'Це тільки тести, що LDAP може синхронізувати правильно. Якщо ваша запит автентифікації LDAP не правильна, користувачі все одно можуть увійти в систему. Ви ПОВИННІ ЗБЕРЕГТИ ВАШ ВИДАЛЕНО ДОДАТНО ДОСТУПНІ ПЕРШЕНЬ.',
    'ldap_manager'              => 'Менеджер LDAP',
    'ldap_server'               => 'Сервер LDAP',
    'ldap_server_help'          => 'This should start with ldap:// (for unencrypted) or ldaps:// (for TLS or SSL)',
    'ldap_server_cert'			=> 'Перевірка SSL сертифікату LDAP',
    'ldap_server_cert_ignore'	=> 'Дозволити недійсний SSL сертифікат',
    'ldap_server_cert_help'		=> 'Виберіть цей прапорець, якщо ви використовуєте самостійно підписаний SSL сертифікат і хотіли б прийняти неприпустимий SSL сертифікат.',
    'ldap_tls'                  => 'Використовувати TLS',
    'ldap_tls_help'             => 'Це необхідно перевірити лише у випадку, якщо Ви запускаєте STARTTLS на вашому сервері LDAP. ',
    'ldap_uname'                => 'LDAP Bind Username',
    'ldap_dept'                 => 'Відділ LDAP',
    'ldap_phone'                => 'LDAP-телефон',
    'ldap_jobtitle'             => 'Заголовок завдання LDAP',
    'ldap_country'              => 'Країни LDAP',
    'ldap_pword'                => 'LDAP-зв\'язуйте пароль',
    'ldap_basedn'               => 'Base Bind DN',
    'ldap_filter'               => 'LDAP-фільтр',
    'ldap_pw_sync'              => 'Синхронізація паролів LDAP',
    'ldap_pw_sync_help'         => 'Зніміть цей прапорець, якщо ви не хочете зберігати паролі LDAP з локальними паролями. Відключення означає, що ваші користувачі можуть не входити в систему, якщо ваш сервер LDAP недосяжний з певних причин.',
    'ldap_username_field'       => 'Поле для імені користувача',
    'ldap_lname_field'          => 'Прізвище',
    'ldap_fname_field'          => 'Ім\'я в LDAP',
    'ldap_auth_filter_query'    => 'LDAP-запит автентифікації',
    'ldap_version'              => 'Версія LDAP',
    'ldap_active_flag'          => 'Активний прапор LDAP',
    'ldap_activated_flag_help'  => 'Це значення використовується для визначення того, чи може синхронізований користувач увійти до Parque Seguro. <strong>Це не впливає на можливість видавати/приймати активи їм/від них</strong> і має бути <strong>назва атрибута</strong> у вашому AD/LDAP, <strong>а не значення</strong>. <br><br>Якщо в цьому полі встановлено назву поля, яка не існує у вашому AD/LDAP, або значення в полі AD/LDAP встановлено на <code>0</code> або <code>false </code>, <strong>вхід користувача буде вимкнено</strong>. Якщо значення в полі AD/LDAP встановлено на <code>1</code> або <code>true</code> або <em>будь-який інший текст</em>, це означає, що користувач може ввійти. Коли поле пусте в Вашому AD, ми дотримуємося атрибута <code>userAccountControl</code>, який зазвичай дозволяє активним користувачам входити в систему.',
    'ldap_emp_num'              => 'LDAP-номер Співробітника',
    'ldap_email'                => 'Email LDAP',
    'ldap_test'                 => 'Тестувати LDAP',
    'ldap_test_sync'            => 'Тестувати синхронізацію LDAP',
    'license'                   => 'Ліцензія програмного забезпечення',
    'load_remote'               => 'Load Remote Avatars',
    'load_remote_help_text'		=> 'Uncheck this box if your install cannot load scripts from the outside internet. This will prevent Parque Seguro from trying load avatars from Gravatar or other outside sources.',
    'login'                     => 'Спроби входу в систему',
    'login_attempt'             => 'Спроби входу в систему',
    'login_ip'                  => 'IP-адреса',
    'login_success'             => 'Успішно?',
    'login_user_agent'          => 'Ідентифікатор браузера',
    'login_help'                => 'Список спроб входу',
    'login_note'                => 'Примітка для входу',
    'login_note_help'           => 'Додайте кілька речень на екран вашого входу, наприклад для того, щоб допомогти людям, які знайшли загубленого чи вкраденого пристрою. Це поле приймає <a href="https://help.github.com/articles/github-flavored-markdown/">Github flavored markdown</a>',
    'login_remote_user_text'    => 'Опції для входу користувача',
    'login_remote_user_enabled_text' => 'Увімкнути вхід за допомогою віддаленого заголовка користувача',
    'login_remote_user_enabled_help' => 'Цей параметр вмикає авторизацію через заголовок REMOTE_USER згідно з заголовком "Загального інтерфейсу шлюзу (rfc3875)"',
    'login_common_disabled_text' => 'Вимкнути інші механізми автентифікації',
    'login_common_disabled_help' => 'Ця опція вимикає інші механізми аутентифікації. Просто увімкніть цю опцію, якщо ви впевнені, що ваш логін на REMOTE_USER вже працює',
    'login_remote_user_custom_logout_url_text' => 'URL спеціального виходу',
    'login_remote_user_custom_logout_url_help' => 'Якщо вказано посилання, користувачі будуть перенаправлені на цей URL після виходу з Parque Seguro. Це корисно для коректного закриття користувацьких сеансів вашого провайдера автентифікації.',
    'login_remote_user_header_name_text' => 'Користувацький заголовок імені користувача',
    'login_remote_user_header_name_help' => 'Використовувати вказаний заголовок замість REMOTE_USER',
    'logo'                    	=> 'Логотип',
    'logo_print_assets'         => 'Використовувати у друку',
    'logo_print_assets_help'    => 'Використовувати брендинг у списки друкованих медіафайлів ',
    'full_multiple_companies_support_help_text' => 'Обмеження користувачів (включаючи адміністраторів), призначених для компаній своїх активів.',
    'full_multiple_companies_support_text' => 'Повна підтримка багатьох компаній',
    'show_in_model_list'   => 'Показати в відкиданнях моделі',
    'optional'					=> 'необов\'язково',
    'per_page'                  => 'Результатів на стор',
    'php'                       => 'Версія PHP',
    'php_info'                  => 'PHP info',
    'php_overview'              => 'PHP',
    'php_overview_keywords'     => 'phpinfo, система, інформація',
    'php_overview_help'         => 'Інформація про систему PHP',
    'php_gd_info'               => 'Ви повинні встановити php-gd, щоб відобразити QR-коди, дивіться інструкції встановлення.',
    'php_gd_warning'            => 'Обробка PHP зображень та GD плагін НЕ встановлені.',
    'pwd_secure_complexity'     => 'Складність пароля',
    'pwd_secure_complexity_help' => 'Виберіть необхідні правила складності паролю.',
    'pwd_secure_complexity_disallow_same_pwd_as_user_fields' => 'Пароль не може бути таким же, як ім\'я, прізвище, електронна пошта або ім\'я користувача',
    'pwd_secure_complexity_letters' => 'Вимагати принаймні одну літеру',
    'pwd_secure_complexity_numbers' => 'Потрібно хоча б один номер',
    'pwd_secure_complexity_symbols' => 'Вимагати принаймні один символ',
    'pwd_secure_complexity_case_diff' => 'Вимагати хоча б один великі і один малу',
    'pwd_secure_min'            => 'Мінімальна кількість символів в паролі',
    'pwd_secure_min_help'       => 'Мінімальне дозволене значення - 8',
    'pwd_secure_uncommon'       => 'Запобігти загальним паролем',
    'pwd_secure_uncommon_help'  => 'Це заборонить користувачам використовувати загальні паролі з топ-10 000 паролів, що повідомляються з порушень.',
    'qr_help'                   => 'Увімкнути QR-коди, щоб спочатку встановити цей',
    'qr_text'                   => 'QR Code Text',
    'saml'                      => 'ПРИРУЧЕННЯ',
    'saml_title'                => 'Оновити налаштування SAML',
    'saml_help'                 => 'Налаштування SAML',
    'saml_enabled'              => 'SAML увімкнено',
    'saml_integration'          => 'Інтеграція з SAML',
    'saml_sp_entityid'          => 'ID сутності',
    'saml_sp_acs_url'           => 'URL-адреса споживачів тверджень (ACS)',
    'saml_sp_sls_url'           => 'Єдиний вихід служби (SLS) URL',
    'saml_sp_x509cert'          => 'Публічний сертифікат',
    'saml_sp_metadata_url'      => 'URL-адреса метаданих',
    'saml_idp_metadata'         => 'SAML IdP метаданих',
    'saml_idp_metadata_help'    => 'Можна вказати метадані IdP за допомогою URL або XML-файлу.',
    'saml_attr_mapping_username' => 'Співставлення атрибутів - Ім\'я користувача',
    'saml_attr_mapping_username_help' => 'NameID буде використано, якщо мапа атрибутів не визначена або неприпустима.',
    'saml_forcelogin_label'     => 'Вхід через SAML',
    'saml_forcelogin'           => 'Зробити SAML основним логіном',
    'saml_forcelogin_help'      => 'Ви можете використовувати \'/login?nosaml\', щоб увійти на звичайну сторінку авторизації.',
    'saml_slo_label'            => 'Єдиний вихід SAML',
    'saml_slo'                  => 'Надіслати запит входу до IdP при виході',
    'saml_slo_help'             => 'Це призведе до того, що користувач буде спочатку перенаправлений до IdP при виході. Залиште невідміченим, якщо IdP не правильно підтримує SP-ініційовані SAML SLO.',
    'saml_custom_settings'      => 'Користувацькі налаштування SAML',
    'saml_custom_settings_help' => 'Ви можете вказати додаткові налаштування до onelogin/php-saml бібліотеки. Використовуйте на свій страх і ризик.',
    'saml_download'             => 'Завантажити метадані',
    'setting'                   => 'Налаштування',
    'settings'                  => 'Налаштування',
    'show_alerts_in_menu'       => 'Показувати сповіщення у верхньому меню',
    'show_archived_in_list'     => 'Архівовані активи',
    'show_archived_in_list_text'     => 'Показати архівовані активи в списку "всіх медіафайлів"',
    'show_assigned_assets'      => 'Показати медіафайли, вибрані медіафайлах',
    'show_assigned_assets_help' => 'Відображати медіафайли, які прив\'язані до інших активів в режимі Перегляд користувача -> Активи, Перегляд користувача -> Інформація -> Друкувати всі призначені для облікового запису -> Показати присвоєні інструменти.',
    'show_images_in_email'     => 'Показувати зображення в е-пошті',
    'show_images_in_email_help'   => 'Зніміть цей прапорець, якщо інсталяція Parque Seguro розташована за VPN або закритою мережею, і користувачі за межами мережі не зможуть завантажувати зображення, які обслуговуються з цієї інсталяції у своїх листах.',
    'site_name'                 => 'Назва сайту',
    'integrations'               => 'Ентеграції',
    'slack'                     => 'Slack',
    'general_webhook'           => 'Загальний Webhook',
    'ms_teams'                  => 'Команди Microsoft',
    'webhook'                   => ':app',
    'webhook_presave'           => 'Тест для збереження',
    'webhook_title'               => 'Зміна параметрів вебхука',
    'webhook_help'                => 'Налаштування інтеграції',
    'webhook_botname'             => ':app Ім\'я',
    'webhook_channel'             => ':app канал',
    'webhook_endpoint'            => ':app Endpoint',
    'webhook_integration'         => ':app Налаштування',
    'webhook_test'                 =>'Перевірити інтеграцію з :app',
    'webhook_integration_help'    => 'Інтеграція :app необов’язкова, однак Endpoint та канал потрібні, якщо ви бажаєте її використовувати. Щоб налаштувати інтеграцію :app, потрібно спочатку <a href=":webhook_link" target="_new" rel="noopener">створити вхідний вебхук</a> у своєму обліковому записі :app. Натисніть кнопку <strong>Тест Інтеграції :app</strong>, щоб підтвердити правильність налаштувань перед збереженням. ',
    'webhook_integration_help_button'    => 'Як тільки ви зберегли свою інформацію :app , з\'явиться тестова кнопка.',
    'webhook_test_help'           => 'Перевірте, чи налаштована інтеграція :app коректно. ВИ ПОВИННІ ЗБЕРЕЖЕТЕ ВАШЕ ОНОВАНО :app SETTINGS FIRST.',
    'snipe_version'  			=> 'Версія Parque Seguro',
    'support_footer'            => 'Підтримка посилань в футері ',
    'support_footer_help'       => 'Вкажіть, хто бачить посилання на інформацію підтримки Parque Seguro та посібник користувача',
    'version_footer'            => 'Версія в футері ',
    'version_footer_help'       => 'Вкажіть, хто бачить версію Parque Seguro і номер збірки.',
    'system'                    => 'Інформація про систему',
    'update'                    => 'Оновити налаштування',
    'value'                     => 'Цінність',
    'brand'                     => 'Фірмове оформлення',
    'brand_keywords'            => 'нижній, логотип, принтер, тема, шкіра, кольори, колір, css',
    'brand_help'                => 'Лого - Назва сайту',
    'web_brand'                 => 'Тип Веб-брендінгу',
    'about_settings_title'      => 'Про налаштування',
    'about_settings_text'       => 'Ці параметри дозволяють налаштувати деякі аспекти інсталяції.',
    'labels_per_page'           => 'Мітки на сторінці',
    'label_dimensions'          => 'Мітки розмірів (у дюймах)',
    'next_auto_tag_base'        => 'Наступна автоіндикатор',
    'page_padding'              => 'Поле сторінки (дюйм)',
    'privacy_policy_link'       => 'Посилання на політику конфіденційності',
    'privacy_policy'            => 'Політика конфіденційності',
    'privacy_policy_link_help'  => 'Якщо тут включено URL, посилання на Вашу політику конфіденційності буде включено в нижній частині додатку і в будь-якому листі, які відправляє система, у відповідності з GDPR. ',
    'purge'                     => 'Знищити вилучені записи',
    'purge_deleted'             => 'Знищити видалено ',
    'labels_display_bgutter'    => 'Позначати нижній шосейний труб',
    'labels_display_sgutter'    => 'Мітка дихання',
    'labels_fontsize'           => 'Label font size',
    'labels_pagewidth'          => 'Ширина аркуша мітки',
    'labels_pageheight'         => 'Висота етикетки',
    'label_gutters'        => 'Інтервал міток (дюйм)',
    'page_dimensions'        => 'Розміри сторінки (дюйм)',
    'label_fields'          => 'Видимі поля для Мітки',
    'inches'        => 'дюймів',
    'width_w'        => 'т',
    'height_h'        => 'г',
    'show_url_in_emails'                => 'Посилання на Parque Seguro в E-mail-адресах',
    'show_url_in_emails_help_text'      => 'Зніміть цей прапорець, якщо ви не хочете вказувати посилання на встановлення Parque Seguro у футерах електронної пошти. Корисно, якщо більшість з ваших користувачів ніколи не увійшли. ',
    'text_pt'        => 'бал',
    'thumbnail_max_h'   => 'Максимальна висота ескізу',
    'thumbnail_max_h_help'   => 'Максимальна висота в пікселях, яку можуть показувати у вигляді списку. Мінімальна 25, макс. 500.',
    'two_factor'        => 'Двофакторна автентифікація',
    'two_factor_secret'        => 'Двофакторний код',
    'two_factor_enrollment'        => 'Двофакторна участь',
    'two_factor_enabled_text'        => 'Увімкнути двофакторну автентифікацію',
    'two_factor_reset'        => 'Скинути двофакторну аутентифікацію',
    'two_factor_reset_help'        => 'Це змусить користувача зареєструвати їх пристрій за допомогою програми авторизації знову. Це може бути корисно, якщо зараз їх загублено або вкрадено в даний час. ',
    'two_factor_reset_success'          => 'Двофакторний пристрій успішно скинуто',
    'two_factor_reset_error'          => 'Не вдалося скинути двофакторний пристрій',
    'two_factor_enabled_warning'        => 'Включення двофакторної аутентифікації, якщо це поки що не ввімкнуто негайно змусить вас автентифікуватися з пристроєм з Google Auth. Ви матимете можливість записувати ваш пристрій, якщо він ще не зареєстрований.',
    'two_factor_enabled_help'        => 'Це дозволить увімкнути двофакторну аутентифікацію за допомогою Генератора кодів Google.',
    'two_factor_optional'        => 'Вибірковий (Користувачі можуть увімкнути або вимкнути, якщо дозволяють)',
    'two_factor_required'        => 'Необхідно для всіх користувачів',
    'two_factor_disabled'        => 'Вимкнено',
    'two_factor_enter_code'	=> 'Введіть двофакторний код',
    'two_factor_config_complete'	=> 'Запропонувати код',
    'two_factor_enabled_edit_not_allowed' => 'Ваш адміністратор не допускає редагування цього параметра.',
    'two_factor_enrollment_text'	=> "Для двофакторної автентифікації необхідно, однак ваш пристрій ще не було встановлено. Відкрийте додаток Google Authenticator і відскануйте QR-код нижче, щоб закріпити ваш пристрій. Після того, як ви увійдете на своєму пристрої, введіть код нижче",
    'require_accept_signature'      => 'Вимагати підпис',
    'require_accept_signature_help_text'      => 'Увімкнення цієї функції для фізичного підписання активів потребує від користувачів.',
    'left'        => 'ліворуч',
    'right'        => 'правий',
    'top'        => 'згори',
    'bottom'        => 'знизу',
    'vertical'        => 'вертикальний',
    'horizontal'        => 'горизонтальний',
    'unique_serial'                => 'Унікальні серійні номери',
    'unique_serial_help_text'                => 'Вибір цієї опції призведе до обмеження унікальності в файлах з медіафайлами',
    'zerofill_count'        => 'Довжина тегів медіафайлів, включаючи нульовий рівень',
    'username_format_help'   => 'Цей параметр буде використовуватися лише процесом імпорту, якщо ім\'я користувача не надано, а ми повинні згенерувати ім\'я користувача.',
    'oauth_title' => 'Налаштування OAuth API',
    'oauth_clients' => 'OAuth Clients',
    'oauth' => 'OAuth',
    'oauth_help' => 'Параметри кінцевої точки Oauth',
    'oauth_no_clients' => 'You have not created any OAuth clients yet.',
    'oauth_secret' => 'Secret',
    'oauth_authorized_apps' => 'Authorized Applications',
    'oauth_redirect_url' => 'Redirect URL',
    'oauth_name_help' => ' Something your users will recognize and trust.',
    'oauth_scopes' => 'Scopes',
    'oauth_callback_url' => 'Your application authorization callback URL.',
    'create_client' => 'Create Client',
    'no_scopes' => 'No scopes',
    'asset_tag_title' => 'Оновити налаштування тегу активу',
    'barcode_title' => 'Оновити налаштування штрих-коду',
    'barcodes' => 'Barcodes',
    'barcodes_help_overview' => 'QR-налаштування штрих-коду &amp; QR',
    'barcodes_help' => 'Це спробує видалити кешовані коди. Це зазвичай, буде використано, лише якщо змінилися налаштування штрих-коду або якщо ваша URL-адреса Parque Seguro змінилася. Код штрих-кодів буде повторно створено при доступі до наступного.',
    'barcodes_spinner' => 'Спроба видалення файлів...',
    'barcode_delete_cache' => 'Видалити кеш штрих-коду',
    'branding_title' => 'Оновити параметри брендінгу',
    'general_title' => 'Оновити загальні налаштування',
    'mail_test' => 'Надіслати тестове повідомлення',
    'mail_test_help' => 'Це спробує надіслати тестовий лист до :replyto.',
    'filter_by_keyword' => 'Фільтрувати за налаштуванням ключового слова',
    'security' => 'Безпека',
    'security_title' => 'Оновити налаштування безпеки',
    'security_keywords' => 'пароль, паролі, вимоги, два фактори, двофакторна аутентифікація, загальні паролі, авторизація, вхід, вхід, авторизація',
    'security_help' => 'Двофакторний, Обмеження паролю',
    'groups_keywords' => 'дозволи, групи дозволів, авторизація',
    'groups_help' => 'Групи дозволів облікового запису',
    'localization' => 'Локалізація',
    'localization_title' => 'Оновити параметри локалізації',
    'localization_keywords' => 'локалізація, валюта, локальна, локальна, часова зона, інтернатізація, мова, мова, переклад',
    'localization_help' => 'Мова, відображення',
    'notifications' => 'Сповіщення',
    'notifications_help' => 'Налаштування e-mail оповіщення та аудиту',
    'asset_tags_help' => 'Збільшення і префіксів',
    'labels' => 'Мітки',
    'labels_title' => 'Оновити налаштування Міток',
    'labels_help' => 'Розміри етикеток і налаштування',
    'purge_keywords' => 'остаточно видалити',
    'purge_help' => 'Знищити вилучені записи',
    'ldap_extension_warning' => 'Схоже, що розширення LDAP встановлено або увімкнено на цьому сервері. Ви все ще можете зберігати ваші налаштування, але вам потрібно буде увімкнути розширення LDAP для PHP перед синхронізацією LDAP або можливістю входу в систему.',
    'ldap_ad' => 'LDAP/реклама',
    'employee_number' => 'Номер співробітника',
    'create_admin_user' => 'Створити користувача ::',
    'create_admin_success' => 'Успіх! Ваш адміністратор був доданий!',
    'create_admin_redirect' => 'Натисніть тут, щоб увійти до свого застосунку!',
    'setup_migrations' => 'Міграції бази даних ::',
    'setup_no_migrations' => 'Не було чого мігрувати. Ваші таблиці бази даних вже налаштовані!',
    'setup_successful_migrations' => 'Ваші таблиці бази даних були створені',
    'setup_migration_output' => 'Перенесення виводу:',
    'setup_migration_create_user' => 'Далі: створити користувача',
    'ldap_settings_link' => 'Сторінка налаштувань LDAP',
    'slack_test' => 'Тест <i class="fab fa-slack"></i> інтеграція',
    'label2_enable'           => 'Новий двигун міток',
    'label2_enable_help'      => 'Перейдіть на нове знаряддя для етикеток. <b>Примітка: вам потрібно буде зберегти це налаштування, перш ніж встановлювати інші.</b>',
    'label2_template'         => 'Шаблон',
    'label2_template_help'    => 'Виберіть, який шаблон використовувати для створення міток',
    'label2_title'            => 'Назва',
    'label2_title_help'       => 'Назва для відображення на етикетках, які підтримують її',
    'label2_title_help_phold' => 'Заповнювач <code>{COMPANY}</code> буде замінено назвою компанії активу',
    'label2_asset_logo'       => 'Використовувати логотип Активу',
    'label2_asset_logo_help'  => 'Використовувати логотип компанії, призначеної активу, а не значення <code>:setting_name</code>',
    'label2_1d_type'          => '1D тип штрихкоду',
    'label2_1d_type_help'     => 'Формат для кодів довжиною 1D',
    'label2_2d_type'          => '2D тип коду',
    'label2_2d_type_help'     => 'Формат для 2D кодів',
    'label2_2d_target'        => '2D код штрих-коду ціль',
    'label2_2d_target_help'   => 'URL точки 2D штрихування при скануванні',
    'label2_fields'           => 'Визначення полів',
    'label2_fields_help'      => 'Поля можна додавати, видаляти та впорядкувати у лівому стовпчику. Для кожного поля декілька варіантів для Мітки і DataSource можна додати, видалити і змінити порядок у правій колонці.',
    'help_asterisk_bold'    => 'Введений <code>**текст**</code> буде відображатися як жирний',
    'help_blank_to_use'     => 'Залиште порожнім, щоб використовувати значення від <code>:setting_name</code>',
    'help_default_will_use' => '<code>:default</code> використовуватиме значення з <code>:setting_name</code>. <br>Зверніть увагу, що для успішної генерації значення штрих-кодів має відповідати відповідним специфікаціям штрих-кодів. Додаткову інформацію дивіться в <a href="https://Parque Seguro.readme.io/docs/barcodes">документації <i class="fa fa-external-link"></i></a>. ',
    'default'               => 'Типово',
    'none'                  => 'Без ефекту',
    'google_callback_help' => 'Це потрібно ввести як URL-адресу зворотного виклику в налаштуваннях програми Google OAuth вашої організації на <strong><a href="https://console.cloud.google.com/" target="_blank">консолі розробника Google <i class="fa fa-external-link" aria-hidden="true"></i></a></strong>.',
    'google_login'      => 'Налаштування входу в робочу область Google',
    'enable_google_login'  => 'Дозволити користувачам входити за допомогою Google Workspace',
    'enable_google_login_help'  => 'Користувачі не будуть автоматично збережені. Вони повинні мати існуючий обліковий запис сюди і в Google Workspace і їх ім\'я тут повинні збігатися з їх адресою електронної пошти в Google Workspace ',
    'mail_reply_to' => 'Поштова адреса',
    'mail_from' => 'Адреса для відправки пошти',
    'database_driver' => 'Драйвер баз даних',
    'bs_table_storage' => 'Складський стіл',
    'timezone' => 'Timezone',
    'profile_edit'          => 'Edit Profile',
    'profile_edit_help'          => 'Allow users to edit their own profiles.',
    'default_avatar' => 'Upload custom default avatar',
    'default_avatar_help' => 'This image will be displayed as a profile if a user does not have a profile photo.',
    'restore_default_avatar' => 'Restore <a href=":default_avatar" data-toggle="lightbox" data-type="image">original system default avatar</a>',
    'restore_default_avatar_help' => '',

];
