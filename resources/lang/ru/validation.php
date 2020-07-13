<?php

return [
	"accepted" => "Вы должны принять \":attribute\".",
	"active_url" => "Поле \":attribute\" содержит недействительный URL.",
	"after" => "В поле \":attribute\" должна быть дата после :date.",
	"after_or_equal" => "The \":attribute\" must be a date after or equal to :date.",
	"alpha" => "Поле \":attribute\" может содержать только буквы.",
	"alpha_dash" => "Поле \":attribute\" может содержать только буквы, цифры и дефис.",
	"alpha_num" => "Поле \":attribute\" может содержать только буквы и цифры.",
	"array" => "Поле \":attribute\" должно быть массивом.",
	"attributes" => [
		"age" => "Возрастная категория",
		"biography" => "Биография",
		"title" => "Заголовок книги",
		"born_date" => "Дата рождения",
		"born_place" => "Место рождения",
		"dead_date" => "Дата смерти",
		"dead_place" => "Место смерти",
		"email" => "Электронная почта",
		"first_name" => "Имя",
		"gender" => "Пол",
		"genres" => "Жанры",
		"home_page" => "Домашняя страница",
		"is_public" => "Книга в общественном достоянии",
		"lang" => "Язык",
		"last_name" => "Фамилия",
		"middle_name" => "Отчество",
		"nickname" => "Ник",
		"orig_first_name" => "Оригинальное имя",
		"orig_last_name" => "Оригинальная фамилия",
		"orig_middle_name" => "Оригинальное отчество",
		"pi_city" => "Город печати",
		"pi_isbn" => "ISBN",
		"pi_pub" => "Издательство",
		"pi_year" => "Год печати",
		"ready_status" => "Статус законченности текста книги",
		"rightholder" => "Правообладатель книги",
		"swear" => "Нецензурная лексика (мат) в тексте книги",
		"ti_lb" => "Язык книги",
		"ti_olb" => "Язык оригинала книги",
		"wikipedia_url" => "Ссылка на википедию",
		"year_public" => "Год перехода в общественное достояние",
		"year_writing" => "Год написания книги",
		"years_creation" => "Годы творчества"
	],
	"before" => "В поле \":attribute\" должна быть дата до :date.",
	"before_or_equal" => "The \":attribute\" must be a date before or equal to :date.",
	"between" => [
		"array" => "Количество элементов в поле \":attribute\" должно быть между :min и :max.",
		"file" => "Размер файла в поле \":attribute\" должен быть между :min и :max Килобайт(а).",
		"numeric" => "Поле \":attribute\" должно быть между :min и :max.",
		"string" => "Количество символов в поле \":attribute\" должно быть между :min и :max."
	],
	"boolean" => "Поле \":attribute\" должно иметь значение логического типа.",
	"confirmed" => "Поле \":attribute\" не совпадает с подтверждением.",
	"custom" => [
		"attribute-name" => [
			"rule-name" => "custom-message"
		],
		"b_ti_lb" => [
			"exists" => "Выбранный язык книги некорректный",
			"required" => "Не указан язык книги"
		],
		"b_ti_olb" => [
			"exists" => "Выбранный язык оригинала книги некорректный"
		],
		"g-recaptcha-response" => [
			"captcha" => "Ошибка капчи! попробуйте позже или сообщите администрации",
			"required" => "Пожалуйста подтвердите, что вы не робот"
		]
	],
	"date" => "Поле \":attribute\" не является датой.",
	"date_format" => "Поле \":attribute\" не соответствует формату :format.",
	"different" => "Поля \":attribute\" и :other должны различаться.",
	"digits" => "Длина цифрового поля \":attribute\" должна быть :digits.",
	"digits_between" => "Длина цифрового поля \":attribute\" должна быть между :min и :max.",
	"dimensions" => "Поле \":attribute\" имеет недопустимые размеры изображения.",
	"distinct" => "Поле \":attribute\" содержит повторяющееся значение.",
	"email" => "Поле \":attribute\" должно быть действительным электронным адресом.",
	"exists" => "Выбранное значение для \":attribute\" некорректно.",
	"file" => "Поле \":attribute\" должно быть файлом.",
	"filled" => "Поле \":attribute\" обязательно для заполнения.",
	"gender" => "Пол указан неверный",
	"image" => "Поле \":attribute\" должно быть изображением.",
	"in" => "Выбранное значение для \":attribute\" ошибочно.",
	"in_array" => "Поле \":attribute\" не существует в :other.",
	"integer" => "Поле \":attribute\" должно быть целым числом.",
	"ip" => "Поле \":attribute\" должно быть действительным IP-адресом.",
	"json" => "Поле \":attribute\" должно быть JSON строкой.",
	"max" => [
		"array" => "Количество элементов в поле \":attribute\" не может превышать :max.",
		"file" => "Размер файла в поле \":attribute\" не может быть более :max Килобайт(а).",
		"numeric" => "Поле \":attribute\" не может быть более :max.",
		"string" => "Количество символов в поле \":attribute\" не может превышать :max."
	],
	"mimes" => "Поле \":attribute\" должно быть файлом одного из следующих типов: :values.",
	"mimetypes" => "Поле \":attribute\" должно быть файлом одного из следующих типов: :values.",
	"min" => [
		"array" => "Количество элементов в поле \":attribute\" должно быть не менее :min.",
		"file" => "Размер файла в поле \":attribute\" должен быть не менее :min Килобайт(а).",
		"numeric" => "Поле \":attribute\" должно быть не менее :min.",
		"string" => "Количество символов в поле \":attribute\" должно быть не менее :min."
	],
	"not_in" => "Выбранное значение для \":attribute\" ошибочно.",
	"numeric" => "Поле \":attribute\" должно быть числом.",
	"present" => "Поле \":attribute\" должно присутствовать.",
	"regex" => "Поле \":attribute\" имеет ошибочный формат.",
	"required" => "Поле \":attribute\" обязательно для заполнения.",
	"required_if" => "Поле \":attribute\" обязательно для заполнения, когда :other равно :value.",
	"required_unless" => "Поле \":attribute\" обязательно для заполнения, когда :other не равно :values.",
	"required_with" => "Поле \":attribute\" обязательно для заполнения, когда :values указано.",
	"required_with_all" => "Поле \":attribute\" обязательно для заполнения, когда :values указано.",
	"required_without" => "Поле \":attribute\" обязательно для заполнения, когда :values не указано.",
	"required_without_all" => "Поле \":attribute\" обязательно для заполнения, когда ни одно из :values не указано.",
	"same" => "Значение \":attribute\" должно совпадать с :other.",
	"size" => [
		"array" => "Количество элементов в поле \":attribute\" должно быть равным :size.",
		"file" => "Размер файла в поле \":attribute\" должен быть равен :size Килобайт(а).",
		"numeric" => "Поле \":attribute\" должно быть равным :size.",
		"string" => "Количество символов в поле \":attribute\" должно быть равным :size."
	],
	"string" => "Поле \":attribute\" должно быть строкой.",
	"timezone" => "Поле \":attribute\" должно быть действительным часовым поясом.",
	"unique" => "Такое значение поля \":attribute\" уже существует.",
	"uploaded" => "Загрузка поля \":attribute\" не удалась.",
	"url" => "Поле \":attribute\" имеет ошибочный формат.",
	"values" => [
		"name_show_type" => [
			"FirstNameLastName" => "Имя Фамилия",
			"FirstnameNicknameLastname" => "Имя Ник Фамилия",
			"FullFirstNameLastName" => "Ник Имя Фамилия",
			"FullLastNameFirstName" => "Ник Фамилия Имя",
			"LastNameFirstName" => "Фамилия Имя",
			"LastnameNicknameFirstname" => "Фамилия Ник Имя",
			"Nick" => "Ник"
		]
	],
	"wikipedia" => "Ссылка не википедию неверная",
	'tempmail' => 'Запрещено использовать временные почтовые ящики',
	'user_email_unique' => "Почтовый ящик уже присутствует в базе данных",
	"not_email" => "Поле \":attribute\" не должно быть электронным адресом.",
	'alpha_single_quote' => 'Поле ":attribute" может содержать только буквы и символ одинарной кавычки.',
	'alpha_left_right' => 'Поле ":attribute" должно содержать букву в начале или в конце слова',
	'clamav' => 'В файле обнаружен вирус',
	'user_nick_unique' => 'Такой ник уже используется',
	'phone' => 'Поле :attribute содержит неверный телефонный номер',
	'credit_card' => [
		'card_invalid' => 'Номер карты неверный',
		'card_length_invalid' => 'Неверная длинна номера карты',
		'card_checksum_invalid' => 'Номер карты неверный'
	],
	'does_not_contain_url' => 'Поле :attribute содержит текст похожий на ссылку. Ссылки запрещены в этом поле',
	'alnum_at_least_three_symbols' => 'Поле :attribute должно содержать минимум 3 буквы или цифры',
	'enum_key' => 'Выбранное значение для ":attribute" некорректно.',
	'enum_value' => 'Выбранное значение для ":attribute" некорректно.',
	'color' => 'Выбранный цвет для поля ":attribute" некорректен.'
];
