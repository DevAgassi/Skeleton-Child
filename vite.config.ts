import { defineSkeletonConfig } from "../Skeleton/vite/config";

// Project build: collects this root's entries and the engine's into one
// manifest. The engine's dist/ is not loaded while this theme is active.
export default defineSkeletonConfig({
  root: __dirname,
  engine: "../Skeleton",
  port: 3001,
});
