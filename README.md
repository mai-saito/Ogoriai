![logo-sm](https://user-images.githubusercontent.com/50275402/114335510-e46a6400-9b87-11eb-89e8-41c219e316c4.png)

Ogoriai（おごりあい）
=================

"Ogoriai" is a smart expense sharing/split app with PHP and MySQL. It allows you to create groups with other users and automatically calculate expenses for each group members once you insert new expense info.

## Demo

1. Mypage / Expense List
![Kapture 2021-04-12 at 14 48 05](https://user-images.githubusercontent.com/50275402/114346635-c2c8a700-9b9e-11eb-8bac-9bb6c311bae8.gif)

2. Add expense
![Kapture 2021-04-12 at 14 49 21](https://user-images.githubusercontent.com/50275402/114346730-e4c22980-9b9e-11eb-8056-8d0c7a8647a1.gif)

3. Create and manage groups
![Kapture 2021-04-12 at 14 50 46](https://user-images.githubusercontent.com/50275402/114346827-03282500-9b9f-11eb-8cd6-1a84fcf3f771.gif)


## Deploy

  http://ogoriai.deci.jp/ogoriai/

## Requirement

* PHP version 7.4.12  
* MySQL version 5.7.32  
* Bootstrap version 4.5.2

## Usage

1. Account registration and sign in

2. Create groups 

  * Create your own group with your selected members. You can search members with their username or emails (wildcard search).
  
  * If you create a group, you will be the leader of the group. Group leaders are allowed to access to the group account interface to add / delete members, update group name / avatar, and delete the group.
  
  * When creating a group, there are 3 choices how to round figures when it cannot split equally. Choices are “round”, “round down”, and “round up”.

2. Add expenses

  * You can add expenses from your mypage under each group tabs. The group tabs show how much you owe / lend within the group, group members, and most 5 recent expense lists.
  
  * There are also buttons for full expense list, settling up, and group account setting. Group account setting is only for the group leaders.
  
3. Expense List

  * It shows full expense list of groups and allows you to modify / delete specific expense data. If you would like to modify, just edit the texts in each cell and click "編集"(modify) button.

  * There are buttons on the right top side where you can find group basic info and create csv files.

4. Export CSV files

5. Settle up group expense.

  * If you go to settle up page and click "Settle up", it will automatically sends notifications to other group members.
  
  REMINDER: Ogoriai cannot physically settle up expenses. You have to use other tools to get back or pay for how much you owe.

6. Admin account

## Install

  The simplest way is to clone ogoriai repository from github. You need to have local server environment that can be installed under your laptop (Like MAMP).
  
  Then, use CLI and insert the line below.

```
$ git clone https://github.com/mai-saito/Ogoriai.git
```

# What to be expected in the future...?

* It will be responsively designed for both computers and mobile environment.

* It will have the better way to settle up the group figures.

and so on...
