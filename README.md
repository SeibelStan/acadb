# acadb v0.1.2

`source/` - основной под проекта

`static/` - результат сборки/кеширования, создаётся сама

`index.php` - основной обработчик

`core.php` - пропроцессоры и вспомогательные функции

`build.php` пересобирает чистые статик-html проекта

`build-index.txt` - цели (станицы) для сборки проекта

Для работы из подпапки указать `ROOT` в `core.php`

`DEV` - всегда собирать страницу, не брать из статики

Поддерживаются чстые php-вставки, markdown, текстовые файлы в оригинальном форматировании