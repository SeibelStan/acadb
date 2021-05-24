{{header}}

{index = explode("\n", file_get_contents('sitemap.txt'));}

<ul>
@foreach $index as $row
    <li><a href="{row}">{row}</a>
@eforeach
</ul>

{{footer}}