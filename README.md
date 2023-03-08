# Poker
Multiplayer browser poker game realized in Texas Holdem system.

The game was made using HTML, CSS, JavaScript, JQuery, Ajax, MySQL and vanilla PHP to learn as much as possible.


Index login page let you register to the game and then log in to the menu with your username and password.
![Login](https://user-images.githubusercontent.com/105807818/223848966-fc7e1aac-c701-48b6-9afe-4a7035a3dbae.png)

Menu page let you choose the table you want to play. Clicking one of the button will direct you to the chosen table.
![Menu](https://user-images.githubusercontent.com/105807818/223848006-e2a7e23f-46c1-4be9-96ad-dbab685db7ff.png)

Game page is already the poker game itself, you will see all the connected players around the table from your perspective, each seat is illuminated with the 3 possible colors, 


White - Waiting for the game/Player who folded,
![Waiting](https://user-images.githubusercontent.com/105807818/223848307-78d5c7a6-4421-4d8f-b2c3-ecd32d2a9d2f.png)

Red - Waiting for the turn during the game,                                                                                                                                
Green - Actual player turn during the game    
![DuringTheGame](https://user-images.githubusercontent.com/105807818/223848374-c10e7eff-f746-4357-8732-fa1923575e24.png)


the game is played according to the rules of the Texas Holdem system, at the first round every player gets two cards, then the game determines Dealer, Small Blind, Big Blind and automatically put they blind bets (150$, 200$) on the table, then everyone has chance to make call, raise or fold. The second round will lay down the three cards on the table - Flop, the third round will lay down the fourth card - Turn, the fourth round will lay down the fifth card - River and at the end of this round the game will determine the winners with the winning hands, once it's done the game will automatically start again.
