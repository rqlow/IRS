<?php

//---------- window alerts and redirects
function AlertWindow($message)
{
	echo "<script language='JavaScript'>window.alert('" . $message . "')</script>";
}

function AlertRedirect($message, $redirectURL)
{
	echo "<script language='JavaScript'>window.alert('$message');window.location='$redirectURL'</script>";
}

function AlertReload($message)
{
	echo "<script language='JavaScript'>window.alert('$message'); window.location=window.location.href;</script>";
}

function AlertOpenRedirect($message, $openURL, $redirectURL)
{
	echo "<script language='JavaScript'>window.alert('$message'); window.open('$openURL'); window.location='$redirectURL';</script>";
}

function AlertClose($message)
{
	echo "<script language='JavaScript'>window.alert('$message'); window.close();</script>";
}

function AlertConfirm($message)
{
	echo "<script language='JavaScript'>return confirm('$message')</script>";
}

function ConfirmWindow($message)
{
	return "return confirm('" . $message . "')";
}

function RedirectWindow($redirectURL)
{
	echo "<script language='JavaScript'>window.location='$redirectURL'</script>";
}

function ReloadWindow()
{
	echo "<script language='JavaScript'>window.location=window.location.href;</script>";
}

function CloseWindow()
{
	echo "<script language='JavaScript'>window.close();</script>";
}

function PrintWindow()
{
	echo "<script language='JavaScript'>window.print();</script>";
}