USE `spot`;

-- Insert dummy users
CALL CREATE_USER(3, 'toby1', 'toby1@localhost.dev', '243279243130246F55497472526247346D434B4543586F694878374165575A484D5230454E654936325866442F6654694E5265334357536F6B706975');
CALL CREATE_USER(3, 'toby2', 'toby2@localhost.dev', '243279243130246F55497472526247346D434B4543586F694878374165575A484D5230454E654936325866442F6654694E5265334357536F6B706975');

-- Same author/owner = toby1
CALL CREATE_POST('Turpis gravida', 'toby1', 'toby1', 'In vel est vel sem pulvinar dapibus et id nisi. Nam quis tincidunt lorem.');
CALL CREATE_POST('Nam quis', 'toby1', 'toby1', 'Liquam pretium mi ut vestibulum cursus. Sed blandit lorem vel blandit.');

-- Same author/owner = toby2
CALL CREATE_POST('Tincidunt purus', 'toby2', 'toby2', 'Nulla feugiat nunc vestibulum turpis gravida blandit. Aenean arcu justo.');
CALL CREATE_POST('Curabitur non feugiat libero', 'toby2', 'toby2', 'Donec pellentesque et tellus vitae imperdiet.');

-- Author = toby 1 | Owner = toby 2
CALL CREATE_POST('Maecenas pretium', 'toby1', 'toby2', 'Suspendisse et nisi non enim dictum pellentesque. Fusce vitae.');
CALL CREATE_POST('Vestibulum dignissim', 'toby1', 'toby2', 'Sed maximus ex eu nisi tincidunt consectetur. Ut lobortis justo.');

-- Author = toby 2 | Owner = toby 1
CALL CREATE_POST('Ut aliquam', 'toby2', 'toby1', 'Aliquam convallis, lacus id molestie pharetra, tortor enim efficitur felis.');
CALL CREATE_POST('Etiam sem', 'toby2', 'toby1', 'Ut maximus ex in mollis tincidunt. Duis bibendum lacinia nisi at tempor.');
