<?php

return [
    'ad'				        => 'Usługa katalogowa Active Directory',
    'ad_domain'				    => 'Domena Active Directory',
    'ad_domain_help'			=> 'Czasami jest taka sama jak domena poczty e-mail, ale nie zawsze.',
    'ad_append_domain_label'    => 'Dołącz nazwę domeny',
    'ad_append_domain'          => 'Dołącz nazwę domeny do pola nazwy użytkownika',
    'ad_append_domain_help'     => 'Użytkownik nie jest wymagany do wpisywania "username@domain.local", może po prostu wpisać "username".',
    'admin_cc_email'            => 'Kopia',
    'admin_cc_email_help'       => 'Jeśli chcesz otrzymywać kopię e-maili przypisań wysyłanych do użytkowników na dodatkowy adres e-mail, wpisz go tutaj. W przeciwnym razie zostaw to pole puste.',
    'admin_settings'            => 'Ustawienia administratora',
    'is_ad'				        => 'To jest serwer Active Directory',
    'alerts'                	=> 'Powiadomienia',
    'alert_title'               => 'Aktualizuj ustawienia powiadomień',
    'alert_email'				=> 'Wyślij powiadomienia do',
    'alert_email_help'    => 'Adresy e-mail lub list dystrybucyjnych, do których mają być wysyłane powiadomienia, oddzielone przecinkami',
    'alerts_enabled'			=> 'Alarmy włączone',
    'alert_interval'			=> 'Próg wygasających alarmów (w dniach)',
    'alert_inv_threshold'		=> 'Inwentarz progu alarmów',
    'allow_user_skin'           => 'Zezwalaj na skórkę użytkownika',
    'allow_user_skin_help_text' => 'Zaznaczenie tego pola pozwoli użytkownikowi zastąpić skórkę interfejsu użytkownika na inną.',
    'asset_ids'					=> 'ID Aktywa',
    'audit_interval'            => 'Interwał audytu',
    'audit_interval_help'       => 'Jeśli jesteś zobowiązany do regularnego fizycznego audytu swoich aktywów, wprowadź interwał w miesiącach, który stosujesz. Jeśli zaktualizujesz tę wartość, wszystkie "daty następnego audytu" dla aktywów z nadchodzącą datą audytu zostaną zaktualizowane.',
    'audit_warning_days'        => 'Próg ostrzegania przed audytem',
    'audit_warning_days_help'   => 'Ile dni wcześniej powinniśmy ostrzec Cię, gdy majątek ma zostać poddany audytowi?',
    'auto_increment_assets'		=> 'Generuj automatycznie zwiększanjące się tagi zasobów',
    'auto_increment_prefix'		=> 'Prefix (opcjonalnie)',
    'auto_incrementing_help'    => 'Włącz automatyczne zwiększanie tagów zasobów aby to ustawić',
    'backups'					=> 'Kopie zapasowe',
    'backups_help'              => 'Utwórz, pobieraj i przywracaj kopie zapasowe ',
    'backups_restoring'         => 'Przywróć z kopii zapasowej',
    'backups_upload'            => 'Prześlij kopię zapasową',
    'backups_path'              => 'Kopie zapasowe na serwerze są przechowywane w <code>:path</code>',
    'backups_restore_warning'   => 'Użyj przycisku przywracania <small><span class="btn btn-xs btn-warning"><i class="text-white fas fa-retweet" aria-hidden="true"></i></span></small> aby przywrócić z poprzedniej kopii zapasowej. (To nie działa obecnie z pamięcią plików S3 lub Docker.<br><br>Twoja baza danych <strong>cała :app_name i wszystkie przesłane pliki zostaną całkowicie zastąpione</strong> przez to, co znajduje się w pliku kopii zapasowej.  ',
    'backups_logged_out'         => 'Wszyscy istniejący użytkownicy, w tym Ty, zostaną wylogowani po zakończeniu przywracania.',
    'backups_large'             => 'Bardzo duże kopie zapasowe mogą przekroczyć limit czasu podczas próby przywrócenia i mogą nadal wymagać uruchomienia za pomocą wiersza poleceń. ',
    'barcode_settings'			=> 'Ustawienia Kodów Kreskowych',
    'confirm_purge'			    => 'Potwierdź wyczyszczenie',
    'confirm_purge_help'		=> 'Wprowadź tekst "DELETE" w poniższym polu, aby wyczyścić usunięte rekordy. Ta akcja nie może zostać cofnięta i będzie NIEZALEŻNIE usuwać wszystkich miękkich elementów i użytkowników. (powinieneś najpierw utworzyć kopię zapasową, po prostu aby być bezpiecznym.)',
    'custom_css'				=> 'Własny CSS',
    'custom_css_help'			=> 'Wprowadź własny kod CSS. Nie używaj tagów &lt;style&gt;&lt;/style&gt;.',
    'custom_forgot_pass_url'	=> 'Niestandardowy adres URL resetowania hasła',
    'custom_forgot_pass_url_help'	=> 'Zastępuje domyślny URL do strony "zapomniałeś hasła?" na ekranie logowania. Pomocne przy potrzebie przekierowania ludzi do własnej strony resetowania hasła LDAP. Uniemożliwi użytkownikowi zresetowanie hasła tradycyjną metodą.',
    'dashboard_message'			=> 'Komunikat pulpitu nawigacyjnego',
    'dashboard_message_help'	=> 'Ten tekst pojawi się na pulpicie nawigacyjnym dla każdego, kto ma uprawnienia do wyświetlania pulpitu.',
    'default_currency'  		=> 'Domyślna Waluta',
    'default_eula_text'			=> 'Domyślna EULA',
    'default_language'			=> 'Domyślny język',
    'default_eula_help_text'	=> 'Możesz również sporządzić własną licencje by sprecyzować kategorie aktywa.',
    'acceptance_note'           => 'Dodaj notatkę do swojej decyzji (opcjonalnie)',
    'display_asset_name'        => 'Wyświetl nazwę aktywa',
    'display_checkout_date'     => 'Wyświetl Datę Przypisania',
    'display_eol'               => 'Wyświetl koniec linii w widoku tabeli',
    'display_qr'                => 'Wyświetlaj QR kody',
    'display_alt_barcode'		=> 'Wyświetlaj kod kreskowy w 1D',
    'email_logo'                => 'Logo w emailach',
    'barcode_type'				=> 'Kod kreskowy typu 2D',
    'alt_barcode_type'			=> 'Kod kreskowy typu 1D',
    'email_logo_size'       => 'Kwadratowe logo wygląda najlepiej w wiadomościach e-mail. ',
    'enabled'                   => 'Włączone',
    'eula_settings'				=> 'Ustawienia Licencji',
    'eula_markdown'				=> 'Ta licencja zezwala na <a href="https://help.github.com/articles/github-flavored-markdown/">Github flavored markdown</a>.',
    'favicon'                   => 'Ikona ulubionych',
    'favicon_format'            => 'Akceptowane typy plików to ico, png i gif. Inne formaty obrazów mogą nie działać we wszystkich przeglądarkach.',
    'favicon_size'          => 'Favikony powinny być kwadratowymi grafikami 16x16 pikseli.',
    'footer_text'               => 'Dodatkowy tekst stopki ',
    'footer_text_help'          => 'Ten tekst pojawi się po prawej stronie stopki. Umieszczanie linków możliwe przy użyciu <a href="https://help.github.com/articles/github-flavored-markdown/">Github flavored markdown</a>. Przejścia linii, nagłowki, obrazki itp. dadzą nieokreślone rezultaty.',
    'general_settings'			=> 'Ustawienia ogólne',
    'general_settings_keywords' => 'obsługa firmy, podpis, akceptacja, format e-mail, format nazwy użytkownika, obrazy, miniatura, eula, grawatar, tos, kokpit menedżerski, prywatność',
    'general_settings_help'     => 'Domyślna licencja i więcej',
    'generate_backup'			=> 'Stwórz Kopie zapasową',
    'google_workspaces'         => 'Obszary robocze Google',
    'header_color'              => 'Kolor nagłówka',
    'info'                      => 'Te ustawienia pozwalają ci zdefiniować najważniejsze szczegóły twojej instalacji.',
    'label_logo'                => 'Logo na etykiecie',
    'label_logo_size'           => 'Najlepiej wygląda logo kwadratowe - będzie wyświetlane w prawym górnym rogu każdej etykiety aktywów. ',
    'laravel'                   => 'Wersja Laravel',
    'ldap'                      => 'LDAP',
    'ldap_default_group'        => 'Domyślną Grupa Uprawnień',
    'ldap_default_group_info'   => 'Wybierz grupę, którą chcesz przypisać do nowo zsynchronizowanych użytkowników. Pamiętaj, że użytkownik przejmuje uprawnienia grupy, do której został przypisany.',
    'no_default_group'          => 'Brak Grupy Domyślnej',
    'ldap_help'                 => 'Usługa katalogowa Active Directory',
    'ldap_client_tls_key'       => 'Klucz TLS klienta LDAP',
    'ldap_client_tls_cert'      => 'Ceryfikat TLS klienta LDAP',
    'ldap_enabled'              => 'LDAP włączone',
    'ldap_integration'          => 'Integracja z LDAP',
    'ldap_settings'             => 'Ustawienia LDAP',
    'ldap_client_tls_cert_help' => 'Certyfikat TLS klienta i klucz dla połączeń LDAP są zwykle użyteczne tylko w konfiguracjach Google Workspace z "Secure LDAP". Wymagane są oba.',
    'ldap_location'             => 'Lokalizacja LDAP',
'ldap_location_help'             => 'Pole lokalizacji Ldap powinno być używane, jeśli <strong>nie jest używane w Bazowym Bind DN.</strong> Pozostaw to pole puste, jeśli używane jest wyszukiwanie OU.',
    'ldap_login_test_help'      => 'Wprowadź poprawną nazwę użytkownika i hasło w podstawowej domenie, którą wprowadziłeś wyżej. W ten sposób przetestujesz czy logowanie LDAP jest poprawnie skonfigurowane. KONIECZNIE ZAPISZ WCZEŚNIEJ SWOJE USTAWIENIA LDAP.',
    'ldap_login_sync_help'      => 'To tylko sprawdza, czy LDAP może poprawnie się synchronizować. Jeśli zapytanie o autoryzację LDAP nie jest poprawne, użytkownicy nadal mogą nie być w stanie się zalogować. NAJPIERW MUSISZ ZAPISAĆ TWOJE WCZEŚNIEJSZE AKTUALIZACJE USTAWIEŃ LDAP.',
    'ldap_manager'              => 'Menedżer LDAP',
    'ldap_server'               => 'Serwery LDAP',
    'ldap_server_help'          => 'This should start with ldap:// (for unencrypted) or ldaps:// (for TLS or SSL)',
    'ldap_server_cert'			=> 'Walidacja certyfikatu SSL dla LDAP',
    'ldap_server_cert_ignore'	=> 'Zezwalaj na nieprawidłowy certyfikat SSL',
    'ldap_server_cert_help'		=> 'Zaznacz tą opcje jeśli używasz certyfikatu SSL podpisanego przez samego siebie i chcesz zezwolić na nieprawidłowy certyfikat.',
    'ldap_tls'                  => 'Używaj TLS',
    'ldap_tls_help'             => 'Ta opcja powinna zaznaczony jedynie gdy używasz STARTLS w swoim serwerze LDAP. ',
    'ldap_uname'                => 'Użytkownik do łączenia się z serwerem LDAP',
    'ldap_dept'                 => 'LDAP Departament',
    'ldap_phone'                => 'LDAP Numery telefonów',
    'ldap_jobtitle'             => 'LDAP Stanowisko',
    'ldap_country'              => 'LDAP Kraj',
    'ldap_pword'                => 'Hasło użytkownika wpisanego do łączenia się z serwerem LDAP',
    'ldap_basedn'               => 'DN',
    'ldap_filter'               => 'Filtr LDAP',
    'ldap_pw_sync'              => 'Synchronizacja haseł LDAP',
    'ldap_pw_sync_help'         => 'Odznacz jeśli nie chcesz synchronizować haseł z LDAP z lokalnymi',
    'ldap_username_field'       => 'Pole użytkownika',
    'ldap_lname_field'          => 'Nazwisko',
    'ldap_fname_field'          => 'Imię',
    'ldap_auth_filter_query'    => 'Autoryzacja LDAP',
    'ldap_version'              => 'Wersja LDAP',
    'ldap_active_flag'          => 'Aktywna flaga LDAP',
    'ldap_activated_flag_help'  => 'Ta wartość służy do określenia, czy zsynchronizowany użytkownik może zalogować się do Parque Seguro. <strong>Nie wpływa na możliwość zaewidencjonowania lub wyewidencjonowania elementów</strong> i powinna być <strong>nazwą atrybutu</strong> w AD/LDAP, <strong>nie wartością</strong> >. <br><br>Jeśli to pole jest ustawione na nazwę pola, która nie istnieje w twoim AD/LDAP lub wartość w polu AD/LDAP jest ustawiona na <code>0</code> lub <code>false </code>, <strong>logowanie użytkownika zostanie wyłączone</strong>. Ustawienie wartości w polu AD/LDAP na <code>1</code> lub <code>true</code> lub <em>dowolny inny tekst</em> oznacza, że użytkownik może się zalogować. Gdy pole jest pusta w AD, szanujemy atrybut <code>userAccountControl</code>, który zwykle umożliwia logowanie niezawieszonym użytkownikom.',
    'ldap_emp_num'              => 'Nr pracownika LDAP',
    'ldap_email'                => 'E-mail pracownika LDAP',
    'ldap_test'                 => 'Test LDAP',
    'ldap_test_sync'            => 'Testuj synchronizację LDAP',
    'license'                   => 'Licencja oprogramowania',
    'load_remote'               => 'Load Remote Avatars',
    'load_remote_help_text'		=> 'Uncheck this box if your install cannot load scripts from the outside internet. This will prevent Parque Seguro from trying load avatars from Gravatar or other outside sources.',
    'login'                     => 'Próby logowania',
    'login_attempt'             => 'Próba logowania',
    'login_ip'                  => 'Adres IP',
    'login_success'             => 'Sukces?',
    'login_user_agent'          => 'Agent użytkownika',
    'login_help'                => 'Lista prób logowania',
    'login_note'                => 'Noty logowania',
    'login_note_help'           => 'Opcjonalnie umieść kilka zdań na ekranie logowania, na przykład w celu pomocy osobom, które znalazły zagubione lub skradzione urządzenia. To pole akceptuje <a href="https://help.github.com/articles/github-flavored-markdown/"> oznaczenia Github </a>',
    'login_remote_user_text'    => 'Opcje logowania zdalnego użytkownika',
    'login_remote_user_enabled_text' => 'Włącz Logowanie za Pomocą Nagłówka Zdalnego Użytkownika',
    'login_remote_user_enabled_help' => 'Ta opcja umożliwia uwierzytelnianie za pośrednictwem nagłówka REMOTE_USER według "Common Gateway Interface (rfc3875)"',
    'login_common_disabled_text' => 'Wyłącz inne mechanizmy uwierzytelniania',
    'login_common_disabled_help' => 'Ta opcja wyłącza inne mechanizmy uwierzytelniania. Po prostu włącz tę opcję, jeśli masz pewność, że twój login REMOTE_USER już działa',
    'login_remote_user_custom_logout_url_text' => 'Niestandardowy adres URL wylogowania',
    'login_remote_user_custom_logout_url_help' => 'Jeżeli URL jest tutaj wpisany, użytkownicy zostaną przekierowani do tego adresu URL po wylogowaniu ze Parque Seguro. Jest to przydatne do prawidłowego zamknięcia sesji użytkownika twojego dostawcy uwierzytelniania.',
    'login_remote_user_header_name_text' => 'Niestandardowy nagłówek nazwy użytkownika',
    'login_remote_user_header_name_help' => 'Użyj określonego nagłówka zamiast REMOTE_USER',
    'logo'                    	=> 'Logo',
    'logo_print_assets'         => 'Użyj Na Wydruku',
    'logo_print_assets_help'    => 'Użyj marki na listach zasobów do wydrukowania',
    'full_multiple_companies_support_help_text' => 'Ograniczenie do użytkowników',
    'full_multiple_companies_support_text' => 'Wsparcie dla wielu firm',
    'show_in_model_list'   => 'Pokaż w Menu Rozwijanym Modelu',
    'optional'					=> 'opcjonalny',
    'per_page'                  => 'Wyników na stronie',
    'php'                       => 'Wersja PHP',
    'php_info'                  => 'PHP info',
    'php_overview'              => 'PHP',
    'php_overview_keywords'     => 'phpinfo, system, info',
    'php_overview_help'         => 'Informacje o systemie PHP',
    'php_gd_info'               => 'Aby wyświetlić kody QR wymagana jest instalacja php-gd, sprawdź instrukcję.',
    'php_gd_warning'            => 'PHP Image Processing i GD plugin nie są zainstalowane.',
    'pwd_secure_complexity'     => 'Złożoności haseł',
    'pwd_secure_complexity_help' => 'Wybierz dowolną regułę złożoności hasła, którą chcesz wymusić.',
    'pwd_secure_complexity_disallow_same_pwd_as_user_fields' => 'Hasło nie może być takie samo jak imię, nazwisko, adres e-mail lub nazwa użytkownika',
    'pwd_secure_complexity_letters' => 'Wymagaj co najmniej jednej litery',
    'pwd_secure_complexity_numbers' => 'Wymagaj co najmniej jednej liczby',
    'pwd_secure_complexity_symbols' => 'Wymaga co najmniej jednego symbolu',
    'pwd_secure_complexity_case_diff' => 'Wymagaj co najmniej jednej wielkiej i jednej małej litery',
    'pwd_secure_min'            => 'Minimalne znaki hasła',
    'pwd_secure_min_help'       => 'Minimalna dozwolona wartość to 8',
    'pwd_secure_uncommon'       => 'Zapobieganie wspólnym hasłom',
    'pwd_secure_uncommon_help'  => 'Uniemożliwi to użytkownikom używanie wspólnych haseł z 10 000 haseł zgłaszanych z naruszeniem.',
    'qr_help'                   => 'Aby użyć tej opcji odblokuj Kody QR',
    'qr_text'                   => 'Tekst kodu QR',
    'saml'                      => 'SAML',
    'saml_title'                => 'Zaktualizuj ustawienia SAML',
    'saml_help'                 => 'Ustawienia SAML',
    'saml_enabled'              => 'SAML włączone',
    'saml_integration'          => 'Integracja z SAML',
    'saml_sp_entityid'          => 'ID jednostki',
    'saml_sp_acs_url'           => 'Adres URL Assertion Consumer Service (ACS)',
    'saml_sp_sls_url'           => 'Adres URL Single Logout Service (SLS)',
    'saml_sp_x509cert'          => 'Publiczny certyfikat',
    'saml_sp_metadata_url'      => 'LDAP Metadane URL',
    'saml_idp_metadata'         => 'Metadane SAML IdP',
    'saml_idp_metadata_help'    => 'Możesz określić metadane IdP za pomocą adresu URL lub pliku XML.',
    'saml_attr_mapping_username' => 'Mapowanie atrybutów - nazwa użytkownika',
    'saml_attr_mapping_username_help' => 'NameID zostanie użyty, jeżeli mapowanie atrybutów nie zostało określone lub jest nieprawidłowe.',
    'saml_forcelogin_label'     => 'Wymuś logowanie SAML',
    'saml_forcelogin'           => 'Ustaw SAML jako główny sposób logowania',
    'saml_forcelogin_help'      => 'Możesz użyć \'/login?nosaml\' aby przejść do normalnej strony logowania.',
    'saml_slo_label'            => 'Single Log Out (SLO) SAML',
    'saml_slo'                  => 'Przy wylogowaniu wyślij LogoutRequest do IdP',
    'saml_slo_help'             => 'To spowoduje, że użytkownik najpierw zostanie przekierowany do IdP przy wylogowaniu. Pozostaw niezaznaczone, jeżeli IdP nie wspiera poprawnie zainizjowanego przez dostawcę usługi (SP-initiated) SAML SLO.',
    'saml_custom_settings'      => 'Ustawienia niestandardowe SAML',
    'saml_custom_settings_help' => 'Możesz określić dodatkowe ustawienia do biblioteki onlogin/php-saml. Używaj na własne ryzyko.',
    'saml_download'             => 'Pobierz metadane',
    'setting'                   => 'Ustawienie',
    'settings'                  => 'Ustawienia',
    'show_alerts_in_menu'       => 'Pokaż ostrzeżenia w górnym menu',
    'show_archived_in_list'     => 'Zarchiwizowane zasoby',
    'show_archived_in_list_text'     => 'Pokaż zarchiwizowane zasoby na liście "wszystkie zasoby"',
    'show_assigned_assets'      => 'Pokaż sprzęty przypisane do sprzętów',
    'show_assigned_assets_help' => 'Wyświetl zasoby, które zostały przypisane do innych zasobów w Widoku użytkownika -> Zasoby, Zobacz użytkownika -> Informacje -> Wydrukuj wszystkie przypisane i w Konto -> Zobacz przypisane zasoby.',
    'show_images_in_email'     => 'Pokaż obrazki w wiadomościach e-mail.',
    'show_images_in_email_help'   => 'Odznacz to pole, jeśli twoja instalacja Parque Seguro znajduje się za siecią VPN lub siecią zamkniętą, a użytkownicy spoza sieci nie będą mogli załadować obrazów obsługiwanych przez tę instalację w swoich wiadomościach e-mail.',
    'site_name'                 => 'Nazwa Witryny',
    'integrations'               => 'Integracje',
    'slack'                     => 'Slack',
    'general_webhook'           => 'Ogólny Webhook',
    'ms_teams'                  => 'Zespoły Microsoft',
    'webhook'                   => ':app',
    'webhook_presave'           => 'Przetestuj, aby zapisać',
    'webhook_title'               => 'Aktualizuj ustawienia webhooka',
    'webhook_help'                => 'Ustawienia integracji',
    'webhook_botname'             => ':app Botname',
    'webhook_channel'             => ':app Kanał',
    'webhook_endpoint'            => ':app Endpoint',
    'webhook_integration'         => 'Ustawienia :app',
    'webhook_test'                 =>'Testuj integrację z :app ',
    'webhook_integration_help'    => 'Integracja z :app jest opcjonalna, jednak endpoint i kanał są wymagane, jeśli chcesz z niej korzystać. Aby skonfigurować integrację z aplikacją, musisz najpierw <a href=":webhook_link" target="_new" rel="noopener">utworzyć przychodzący webhook</a> na swoim koncie :App. Kliknij przycisk <strong>Test :app Integration</strong> , aby potwierdzić poprawność ustawień przed zapisaniem. ',
    'webhook_integration_help_button'    => 'Po zapisaniu informacji o :app pojawi się przycisk testowy.',
    'webhook_test_help'           => 'Sprawdź, czy integracja aplikacji jest poprawnie skonfigurowana. ZAPISZ SWOJE AKTUALIZOWANE :app USTAWIENIA.',
    'snipe_version'  			=> 'Wersja Parque Seguro',
    'support_footer'            => 'Obsługa linków stopki ',
    'support_footer_help'       => 'Określ kto widzi linki do Parque Seguro Instrukcji Obsługi oraz Wsparcia',
    'version_footer'            => 'Wersja w stopce ',
    'version_footer_help'       => 'Określ, kto widzi wersję oraz numer kompilacji Parque Seguro.',
    'system'                    => 'Informacje o Systemie',
    'update'                    => 'Ustawienia Aktualizacji',
    'value'                     => 'Wartość',
    'brand'                     => 'Nagłówek',
    'brand_keywords'            => 'stopka, logo, druk, motyw, skórka, nagłówek, kolory, kolor, css',
    'brand_help'                => 'Logo, Nazwa witryny',
    'web_brand'                 => 'Typ markowania witryny',
    'about_settings_title'      => 'O Ustawieniach',
    'about_settings_text'       => 'Te ustawienia pozwalają ci zmodyfikować najważniejsze szczegóły twojej instalacji.',
    'labels_per_page'           => 'Etykieta per strona',
    'label_dimensions'          => 'rozmiar etykiety',
    'next_auto_tag_base'        => 'Następny automatyczny przyrost',
    'page_padding'              => 'Margines strony (cale)',
    'privacy_policy_link'       => 'Link do Polityki prywatności',
    'privacy_policy'            => 'Polityka prywatności',
    'privacy_policy_link_help'  => 'Jeśli adres URL znajduje się tutaj, link do polityki prywatności zostanie umieszczony w stopce aplikacji oraz we wszystkich wiadomościach e-mail wysyłanych przez system zgodnie z GDPR.',
    'purge'                     => 'Wyczyść usunięte rekordy',
    'purge_deleted'             => 'Wyczyść usunięte ',
    'labels_display_bgutter'    => 'Etykieta z rynną dolną',
    'labels_display_sgutter'    => 'Etykieta z rynną boczną',
    'labels_fontsize'           => 'Rozmiar czcionki na etykiecie',
    'labels_pagewidth'          => 'Szerokość arkusza etykiety',
    'labels_pageheight'         => 'Wysokość arkusza etykiet',
    'label_gutters'        => 'Rozstaw etykiet (cale)',
    'page_dimensions'        => 'Margines strony (cale)',
    'label_fields'          => 'Widoczne póla etykiet',
    'inches'        => 'cale',
    'width_w'        => 'szerokość',
    'height_h'        => 'wysokość',
    'show_url_in_emails'                => 'Połącz się z Parque Seguro w wiadomościach e-mail',
    'show_url_in_emails_help_text'      => 'Usuń zaznaczenie tego pola, jeśli nie chcesz łączyć się z instalacją Parque Seguro w stopkach wiadomości e-mail. Przydatne, jeśli większość użytkowników nigdy nie loguje się.',
    'text_pt'        => 'piksel',
    'thumbnail_max_h'   => 'Max wysokość miniatur',
    'thumbnail_max_h_help'   => 'Maksymalna wysokość w pikselach, które miniatury mogą wyświetlać w widoku aukcji. Min 25, maks. 500.',
    'two_factor'        => 'Autoryzacja dwuskładnikowa',
    'two_factor_secret'        => 'Kod jednorazowy',
    'two_factor_enrollment'        => 'Rejestracja dwóch czynników',
    'two_factor_enabled_text'        => 'Włącz uwieżytelnianie dwuskładnikowe',
    'two_factor_reset'        => 'Zresetować dwuskładnikowy klucz',
    'two_factor_reset_help'        => 'Spowoduje to zmuszenie użytkownika do zapisania urządzenia do aplikacji uwierzytelniającej. Może to być użyteczne w przypadku zagubienia lub kradzieży aktualnie zamontowanego urządzenia. ',
    'two_factor_reset_success'          => 'Dwa urządzenia współczynnikowe z powodzeniem zresetowane',
    'two_factor_reset_error'          => 'Nie udało się zresetować urządzenia',
    'two_factor_enabled_warning'        => 'Włączenie dwóch czynników, jeśli nie jest aktualnie włączone, natychmiast zmusi Cię do uwierzytelnienia przy użyciu urządzenia z certyfikatem Google Authentication. Będziesz mieć możliwość zapisania urządzenia, jeśli nie jest on aktualnie zapisany.',
    'two_factor_enabled_help'        => 'Włączy to uwierzytelnianie dwuskładnikowe za pomocą narzędzia Google Authenticator.',
    'two_factor_optional'        => 'Wybiórczo (Użytkownicy mogą włączyć lub wyłączyć jeśli posiadają uprawnienie)',
    'two_factor_required'        => 'Wymagane dla wszystkich użytkowników',
    'two_factor_disabled'        => 'Wyłączony',
    'two_factor_enter_code'	=> 'Wprowadź kod jednorazowy',
    'two_factor_config_complete'	=> 'Zatwierdź kod',
    'two_factor_enabled_edit_not_allowed' => 'Administrator nie zezwala na edycję tego ustawienia.',
    'two_factor_enrollment_text'	=> "Wymagane jest uwierzytelnianie dwóch elementów, ale urządzenie nie zostało jeszcze zapisane. Otwórz aplikację Google Authenticator i zeskanuj kod QR poniżej, aby zarejestrować urządzenie. Po zarejestrowaniu urządzenia wprowadź poniższy kod",
    'require_accept_signature'      => 'Wymagany podpis',
    'require_accept_signature_help_text'      => 'Włączając tę funkcjonalność wymusza się na użytkownikach fizycznego podpisania przyjęcia aktywa.',
    'left'        => 'lewo',
    'right'        => 'prawo',
    'top'        => 'góra',
    'bottom'        => 'dół',
    'vertical'        => 'pionowy',
    'horizontal'        => 'poziomy',
    'unique_serial'                => 'Unikalne numery seryjne',
    'unique_serial_help_text'                => 'Zaznaczenie tego pola wymusi sprawdzanie czy numer seryjny nie został już przypisany w zasobach',
    'zerofill_count'        => 'Długość znaczników zasobów, w tym zerofill',
    'username_format_help'   => 'To ustawienie będzie używane przez proces importu tylko wtedy, gdy nazwa użytkownika nie jest podana i musimy wygenerować nazwę użytkownika dla Ciebie.',
    'oauth_title' => 'Ustawienia API OAuth',
    'oauth_clients' => 'Klienci OAuth',
    'oauth' => 'OAuth',
    'oauth_help' => 'Ustawienia punktu końcowego Oauth',
    'oauth_no_clients' => 'Nie utworzyłeś jeszcze żadnych klientów OAuth.',
    'oauth_secret' => 'Sekret',
    'oauth_authorized_apps' => 'Authorized Applications',
    'oauth_redirect_url' => 'URL przekierowania',
    'oauth_name_help' => ' Something your users will recognize and trust.',
    'oauth_scopes' => 'Scopes',
    'oauth_callback_url' => 'Your application authorization callback URL.',
    'create_client' => 'Create Client',
    'no_scopes' => 'No scopes',
    'asset_tag_title' => 'Aktualizuj ustawienia tagów zasobów',
    'barcode_title' => 'Aktualizuj ustawienia kodów kreskowych',
    'barcodes' => 'Kody kreskowe',
    'barcodes_help_overview' => 'Kod kreskowy &amp; Ustawienia QR',
    'barcodes_help' => 'Spowoduje to próbę usunięcia kodów kreskowych z pamięci podręcznej. Jest to zwykle używane tylko wtedy, gdy zmieniły się ustawienia kodu kreskowego lub jeśli zmienił się adres URL Parque Seguro. Kody kreskowe zostaną wygenerowane ponownie przy następnym dostępie.',
    'barcodes_spinner' => 'Próba usunięcia plików...',
    'barcode_delete_cache' => 'Usuń pamięć podręczną kodu kreskowego',
    'branding_title' => 'Aktualizuj ustawienia wyglądu',
    'general_title' => 'Aktualizuj ustawienia ogólne',
    'mail_test' => 'Wyślij wiadomość testową',
    'mail_test_help' => 'Spowoduje to próbę wysłania wiadomości testowej do :replyto.',
    'filter_by_keyword' => 'Filtruj przez ustawienie słowa kluczowego',
    'security' => 'Bezpieczeństwo',
    'security_title' => 'Aktualizuj ustawienia zabezpieczeń',
    'security_keywords' => 'hasło, hasła, wymagania, dwuskładnikowe, dwuskładnikowe, wspólne hasła, zdalne logowanie, wylogowanie, uwierzytelnianie',
    'security_help' => 'Weryfikacja dwuetapowa, wymagania haseł',
    'groups_keywords' => 'uprawnienia, grupy uprawnień, autoryzacje',
    'groups_help' => 'Grupy uprawnień',
    'localization' => 'Lokalizacja',
    'localization_title' => 'Aktualizuj ustawienia lokalizacji',
    'localization_keywords' => 'lokalizacja, waluta, lokalna, lokalna, lokalna, strefa czasowa, strefa czasowa, międzynarodowa, internatalizacja, język, tłumaczenie',
    'localization_help' => 'Język, wyświetlanie daty',
    'notifications' => 'Powiadomienia',
    'notifications_help' => 'Powiadomienia e-mail i ustawienia audytu',
    'asset_tags_help' => 'Zwiększanie i prefiksy',
    'labels' => 'Etykiety',
    'labels_title' => 'Aktualizuj ustawienia etykiety',
    'labels_help' => 'Rozmiary etykiet i ustawienia',
    'purge_keywords' => 'trwałe usunięcie',
    'purge_help' => 'Wyczyść usunięte rekordy',
    'ldap_extension_warning' => 'Nie wygląda na to, że rozszerzenie LDAP jest zainstalowane lub włączone na tym serwerze. Nadal możesz zapisać swoje ustawienia, ale musisz włączyć rozszerzenie LDAP dla PHP, zanim synchronizacja lub logowanie LDAP zadziała.',
    'ldap_ad' => 'LDAP/AD',
    'employee_number' => 'Numer pracownika',
    'create_admin_user' => 'Dodaj użytkownika ::',
    'create_admin_success' => 'Sukces! Twój użytkownik administratracyjny został dodany!',
    'create_admin_redirect' => 'Kliknij tutaj, aby przejść do logowania aplikacji!',
    'setup_migrations' => 'Migracje bazy danych ::',
    'setup_no_migrations' => 'Nie było nic do migracji. Twoje tabele bazy danych zostały już skonfigurowane!',
    'setup_successful_migrations' => 'Twoje tabele bazy danych zostały utworzone',
    'setup_migration_output' => 'Wyniki migracji:',
    'setup_migration_create_user' => 'Następnie: Stwórz użytkownika',
    'ldap_settings_link' => 'Ustawienia LDAP',
    'slack_test' => 'Test integracji <i class="fab fa-slack"></i>',
    'label2_enable'           => 'Nowy silnik etykiet',
    'label2_enable_help'      => 'Przełącz na nowy silnik etykiet. <b>Uwaga: musisz zapisać to ustawienie przed ustawieniem innych.</b>',
    'label2_template'         => 'Szablon',
    'label2_template_help'    => 'Wybierz szablon używany do generowania etykiet',
    'label2_title'            => 'Tytuł',
    'label2_title_help'       => 'Tytuł wyświetlany na etykietach, które ją obsługują',
    'label2_title_help_phold' => 'Symbol zastępczy <code>{COMPANY}</code> zostanie zastąpiony nazwą firmy&apos;',
    'label2_asset_logo'       => 'Użyj logo aktywów',
    'label2_asset_logo_help'  => 'Użyj logo przypisanej firmy&apos;s, a nie wartości <code>:setting_name</code>',
    'label2_1d_type'          => '1D Kod kreskowy',
    'label2_1d_type_help'     => 'Format kodów kreskowych 1D',
    'label2_2d_type'          => 'Kod kreskowy typu 2D',
    'label2_2d_type_help'     => 'Format kodów kreskowych 2D',
    'label2_2d_target'        => '2D Kod kreskowy',
    'label2_2d_target_help'   => 'Adres URL kodów kreskowych 2D w przypadku skanowania',
    'label2_fields'           => 'Definicje pól',
    'label2_fields_help'      => 'Pola mogą być dodawane, usuwane i przesuwane w lewej kolumnie. Dla każdego pola wiele opcji etykiet i źródeł danych może być dodawanych, usuwanych i zmienianych w prawej kolumnie.',
    'help_asterisk_bold'    => 'Tekst wprowadzony jako <code>**text**</code> będzie wyświetlany jako pogrubiony',
    'help_blank_to_use'     => 'Pozostaw puste, aby użyć wartości z <code>:setting_name</code>',
    'help_default_will_use' => '<code>:default</code> użyje wartości z <code>:setting_name</code>. <br>Zauważ, że wartość kodów kreskowych musi być zgodna z odpowiednią specyfikacją kodu kreskowego, aby mogła zostać wygenerowana. Aby uzyskać więcej informacji zapoznaj się z <a href="https://Parque Seguro.readme.io/docs/barcodes">dokumentacją <i class="fa fa-external-link"></i></a>. ',
    'default'               => 'Domyślny',
    'none'                  => 'Brak',
    'google_callback_help' => 'Należy go wprowadzić jako adres URL wywołania zwrotnego w ustawieniach aplikacji Google OAuth w &apos;s <strong><a href="https://console.cloud.google.com/" target="_blank">konsoli programisty Google <i class="fa fa-external-link" aria-hidden="true"></i></a></strong>.',
    'google_login'      => 'Ustawienia logowania Google Workspace',
    'enable_google_login'  => 'Włącz logowanie przez Google Workspace',
    'enable_google_login_help'  => 'Użytkownicy nie będą automatycznie tworzeni. Muszą mieć istniejące konto tutaj i w Google Workspace, a ich nazwa użytkownika musi pasować do ich adresu e-mail w obszarze roboczym Google. ',
    'mail_reply_to' => 'Adres e-mail odpowiedzi',
    'mail_from' => 'Adres nadawcy',
    'database_driver' => 'Sterownik bazy danych',
    'bs_table_storage' => 'Pamięć tabeli',
    'timezone' => 'Strefa czasowa',
    'profile_edit'          => 'Edytuj profil',
    'profile_edit_help'          => 'Allow users to edit their own profiles.',
    'default_avatar' => 'Upload custom default avatar',
    'default_avatar_help' => 'This image will be displayed as a profile if a user does not have a profile photo.',
    'restore_default_avatar' => 'Restore <a href=":default_avatar" data-toggle="lightbox" data-type="image">original system default avatar</a>',
    'restore_default_avatar_help' => '',

];
