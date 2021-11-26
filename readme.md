##Roadmap:

1. **Багфиксы.**
   - [ ] *[release-blocker]* Вынести форматирование кнопок в отдельный класс
   - [ ] *[release-blocker]* Переиспользовать это форматирование в других экшенах
   - [ ] *[release-blocker]* Доделать `TelegramMessageGenerator`: передавать `Subscriber` и разобраться с кнопками
   - [ ] *[release-blocker]* Протестировать получившееся
2. **Развитие**
   - Настраивать, на какие именно события юзер подписан.
     - [ ] *[release-blocker]* Суммарно по realtime
     - [ ] *[release-blocker]* Суммарно по summary
     - [ ] Отдельно для каждого репозитория внутри настроек realtime/summary. Если для репы такое настроено, то эта настройка приоритетнее предыдущих двух. Этот пункт - на после релиза.
   - [ ] Настройка частоты и времени отправки summary
   - [ ] Настройка часового пояса для времени отправки summary
   - Добавить сообщения о принятии пожертвований любых сумм
     - [ ] Принять решение о том, в каком виде, где и когда эти сообщения будут видны 
     - [ ] Прикладывать ссылку на OpenCollective
     - [ ] Принимать в криптовалюте (сперва посоветоваться с samdark)
   - [ ] Добавить подписку авторам на события по их PR/Issue. Чтобы приходили сообщения о провалившихся тестах в PR, комментариях в нем или issue, о мерже, смене лейблов и т.п. Т.е. юзер телеги должен будет указать свой гитхабовский юзернейм.
   - [ ] Локализация, перевод на английский язык и использование локализованных текстов в целом, сохранение локали юзера.
   - Дополнительные режимы формата summary
     - [ ] Не присылать realtime события ночью, а утром присылать summary по всем пропущенным
     - [ ] По кнопке присылать summary тех событий, что произошли после предыдущего получения summary
     - [ ] Summary за указанный период времени
     - [ ] Настройка частоты автоматически присылаемых summary
