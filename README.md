# php-class-graph

Walking your PHP project to make relational graph chart.

:warning: This project is under experimental. Maybe some part is not implemented yet. Please report if you find issue.


## Usage

1. Ready tools
    - [Graphviz](https://graphviz.gitlab.io/download/)
    - If use MacOS and iTerm2, it possible for simply use with [`imgcat`](https://www.iterm2.com/documentation-images.html).
1. `git clone` this repo.
1. `composer install`
1. `php examples/whole_project.php -p <YOUR PROJECT PATH> -d dot | dot -T png | imgcat`

e.g. `php examples/whole_project.php -p . -d dot | dot -T png | imgcat`
![image](https://user-images.githubusercontent.com/1658147/76247220-87b0c180-6282-11ea-8cb5-eb84288f3508.png)
