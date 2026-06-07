"use client";

import { ApiReferenceReact } from "@scalar/api-reference-react";
import "@scalar/api-reference-react/style.css";
import { SandboxKeyBanner } from "../../components/SandboxKeyBanner";

export default function Reference() {
  return (
    <div>
      <SandboxKeyBanner />
      <ApiReferenceReact
        configuration={{
          url: "/openapi.yaml",
          theme: "deepSpace",
          layout: "modern",
          darkMode: true,
          hideClientButton: false,
          hideDownloadButton: false,
          metaData: {
            title: "Webhook Relay — API reference",
          },
          servers: [
            {
              url: "https://api.webhook-relay.dcsuniverse.com",
              description: "Production",
            },
          ],
        }}
      />
    </div>
  );
}
