# CustomerNewTicket — FreeScout Module

Adds a **New Ticket** button directly to customer profile pages in FreeScout. When clicked it opens the ticket creation form with the customer's email address pre-filled. If the agent has access to more than one mailbox a dropdown lets them pick which mailbox to use.

## Features

- **One-click ticket creation** from any customer profile page — no need to navigate away and manually type the customer's email.
- **Mailbox-aware** — only shows mailboxes the logged-in agent has permission to view.
- **Single mailbox shortcut** — if the agent only has access to one mailbox, clicking the button goes straight to the new ticket form with no dropdown needed.
- **Plugin-compatible** — the button also appears on customer profile tabs provided by other modules (e.g. StoklySync).

## Requirements

- FreeScout **1.8.181** or later
- The official **Customers Management (CRM)** module must be installed and active

## Installation

1. Download or clone this repository into your FreeScout modules directory:

   ```
   /path/to/freescout/Modules/CustomerNewTicket/
   ```

2. In FreeScout go to **Admin → Modules** and activate **Customer New Ticket**.

## Usage

Once active, open any customer profile page. An envelope icon button appears next to the existing action menu in the customer profile header:

- **Single mailbox** — the button is a direct link; clicking it opens the new ticket form with the customer's email pre-filled.
- **Multiple mailboxes** — the button opens a dropdown listing your accessible mailboxes; selecting one opens the new ticket form for that mailbox.

No configuration is required — the module works automatically based on the logged-in agent's mailbox permissions.

## How it works

The module hooks into FreeScout's event system (Eventy) to:

- Inject the button CSS and JavaScript on every page
- Detect when the current page is a customer profile view
- Resolve the customer's email address and the agent's accessible mailboxes server-side
- Output these as JavaScript variables which the frontend uses to render the correct button or dropdown

## Author

Built by [Hamlet Digital](https://hamlet-digital.co.uk)

## License

[AGPL-3.0-or-later](LICENSE) — GNU Affero General Public License v3.0 or later.
