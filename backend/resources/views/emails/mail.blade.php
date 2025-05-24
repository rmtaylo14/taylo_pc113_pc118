@component('mail::message')
<div style="text-align: center;">

    <h2>Hi {{ $firstname }}!</h2>
    <br>
    Welcome to <b>kerker</b>
    <br>
    Your Account has been created successfully.
</div>
<br>
<br>
<div style="display: flex; justify-content: center;">
    <a href="https://frontend-folder.test/setup.php?id={{$id}}"
        style="padding: 10px 20px; background-color: #ffbf00; color: #000000; text-decoration: none; border-radius: 5px;">
        Complete Your Account
    </a>
</div>


Thanks, <br>

@endcomponent