module.exports = {
  content: [
    "./frontend/**/*.php",
    "./backend/includes/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        ink: "#080808",
        coal: "#121212",
        smoke: "#1c1c1c",
        brass: "#c8a96b",
        amberglow: "#f5d9a0",
        blush: "#f9ece7",
      },
      fontFamily: {
        display: ['Georgia', 'Times New Roman', 'serif'],
        body: ['ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        aura: "0 24px 80px rgba(0, 0, 0, 0.45)",
      },
      backgroundImage: {
        "hero-glow":
          "radial-gradient(circle at top, rgba(200, 169, 107, 0.2), transparent 32%), linear-gradient(180deg, rgba(12, 12, 12, 0.4), rgba(8, 8, 8, 0.96))",
      },
    },
  },
  plugins: [],
};
