{{header}}

{index = explode("\n", file_get_contents('build-index.txt'));}

<ul>
@foreach $index as $row
    <li><a href="{row}">{row}</a>
@eforeach
</ul>

{{footer}}