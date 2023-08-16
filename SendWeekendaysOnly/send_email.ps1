$Date = Get-Date
$EmailFrom = "example@gmail.com"
$EmailTo = "example@gmail.com"
$Subject = "Test Notice"
$SMTPServer = "smtp.gmail.com"

$SMTPClient = New-Object Net.Mail.SmtpClient($SMTPServer, 587)
$SMTPClient.EnableSsl = $true
$SMTPClient.Credentials = New-Object System.Net.NetworkCredential($EmailFrom, "Password Here")

$dayOfMonth = $Date.Day
$dayOfWeek = $Date.DayOfWeek

# Calculate the date of the Friday before the 16th
$dayOfWeek16th = (Get-Date -Year $Date.Year -Month $Date.Month -Day 16).DayOfWeek
if ($dayOfWeek16th -eq "Saturday") {
    $fridayBefore16th = (Get-Date -Year $Date.Year -Month $Date.Month -Day 15).AddDays(-1)
} elseif ($dayOfWeek16th -eq "Sunday") {
    $fridayBefore16th = (Get-Date -Year $Date.Year -Month $Date.Month -Day 15).AddDays(-2)
}

# Check if today is the Friday before the 16th
if ($fridayBefore16th -and $Date.Date -eq $fridayBefore16th.Date) {
    $Body = "The 16th lands on a weekend"
    if ($fridayBefore16th.Date -eq $Date.Date) {
        $SMTPClient.Send($EmailFrom, $EmailTo, $Subject, $Body)
    }
} else {
    $Body = "The 16th doesn't land on a weekend"
    if ($dayOfMonth -eq 16) {
        $SMTPClient.Send($EmailFrom, $EmailTo, $Subject, $Body)
    }
}
