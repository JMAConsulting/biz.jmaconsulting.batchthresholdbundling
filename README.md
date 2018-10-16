# biz.jmaconsulting.batchthresholdbundling

This extension bundle small transactions into single entry during contribution batch export.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM v5.7+

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl biz.jmaconsulting.batchthresholdbundling@https://github.com/FIXME/biz.jmaconsulting.batchthresholdbundling/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/biz.jmaconsulting.batchthresholdbundling.git
cv en batchthresholdbundling
```

## Usage

Currently this extension bundle up contribution's financial entries based on threshold amount as per CiviContribute setting 'Contribution threshold amount for bundling', before translated into General Ledger entries for Sage Intacct.
