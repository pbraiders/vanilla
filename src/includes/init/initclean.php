<?php
/*************************************************************************
 *                                                                       *
 * Copyright (C) 2010   Olivier JULLIEN - PBRAIDERS.COM                  *
 * Tous droits réservés - All rights reserved                            *
 *                                                                       *
 *************************************************************************
 *                                                                       *
 * Except if expressly provided in a dedicated License Agreement,you     *
 * are not authorized to:                                                *
 *                                                                       *
 * 1. Use,copy,modify or transfer this software component,module or      *
 * product,including any accompanying electronic or paper documentation  *
 * (together,the "Software").                                            *
 *                                                                       *
 * 2. Remove any product identification,copyright,proprietary notices    *
 * or labels from the Software.                                          *
 *                                                                       *
 * 3. Modify,reverse engineer,decompile,disassemble or otherwise         *
 * attempt to reconstruct or discover the source code,or any parts of    *
 * it,from the binaries of the Software.                                 *
 *                                                                       *
 * 4. Create derivative works based on the Software (e.g. incorporating  *
 * the Software in another software or commercial product or service     *
 * without a proper license).                                            *
 *                                                                       *
 * By installing or using the "Software",you confirm your acceptance     *
 * of the hereabove terms and conditions.                                *
 *                                                                       *
 *************************************************************************/
/*************************************************************************
 * file encoding: UTF-8
 * description: Delete instancied objects
 * author: Olivier JULLIEN - 2010-02-04
 * update: Olivier JULLIEN - 2010-05-24 - Remove TraceWarning
 *                                        Add CLog
 *************************************************************************/
if( !defined('PBR_VERSION') )
    die('-1');

    if(defined('PBR_DB_LOADED')) CDb::DeleteInstance();
    if(defined('PBR_CONTACT_LOADED')) CContact::DeleteInstance();
    if(defined('PBR_SESSION_LOADED')) CSession::DeleteInstance();
    if(defined('PBR_DATE_LOADED')) CDate::DeleteInstance();
    if(defined('PBR_RENT_LOADED')) CRent::DeleteInstance();
    if(defined('PBR_PAGE_LOADED')) CPaging::DeleteInstance();
    if(defined('PBR_NEWUSER_LOADED')) CNewUser::DeleteInstance();
    if(defined('PBR_LOG_LOADED')) CLog::DeleteInstance();
    CUser::DeleteInstance();
    CCookie::DeleteInstance();
    CErrorList::DeleteInstance();
?>
