document.addEventListener('DOMContentLoaded', () => {
  const constellationContainer = document.getElementById('constellation-container');
  const numNodes = 30; // Number of file/folder nodes
  const minSize = 10;
  const maxSize = 30;
  const minSpeed = 0.5;
  const maxSpeed = 2;
  const colors = ['#aaffaa', '#aaaaff', '#ffaaff', '#ffffaa', '#aaffff'];

  const nodes = [];

  function createNode() {
      const node = document.createElement('div');
      node.classList.add('constellation-node');
      const size = Math.random() * (maxSize - minSize) + minSize;
      node.style.width = `${size}px`;
      node.style.height = `${size}px`;
      node.style.borderRadius = '50%';
      node.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
      node.style.opacity = Math.random() * 0.7 + 0.3; // Vary opacity
      node.style.position = 'absolute';
      node.style.left = `${Math.random() * 100}%`;
      node.style.top = `${Math.random() * 100}%`;
      node.vx = (Math.random() - 0.5) * (maxSpeed - minSpeed) + minSpeed; // Velocity X
      node.vy = (Math.random() - 0.5) * (maxSpeed - minSpeed) + minSpeed; // Velocity Y
      node.addEventListener('mouseover', () => {
          node.style.transform = 'scale(1.2)';
          node.style.opacity = 1;
      });
      node.addEventListener('mouseout', () => {
          node.style.transform = 'scale(1)';
          node.style.opacity = Math.random() * 0.7 + 0.3;
      });
      constellationContainer.appendChild(node);
      nodes.push(node);
  }

  for (let i = 0; i < numNodes; i++) {
      createNode();
  }

  function updateConstellation() {
      nodes.forEach(node => {
          let x = parseFloat(node.style.left);
          let y = parseFloat(node.style.top);
          const width = constellationContainer.offsetWidth;
          const height = constellationContainer.offsetHeight;

          x += node.vx * 0.02; // Adjust speed multiplier
          y += node.vy * 0.02;

          if (x < 0 || x > 100) node.vx *= -1;
          if (y < 0 || y > 100) node.vy *= -1;

          node.style.left = `${x}%`;
          node.style.top = `${y}%`;
      });
      requestAnimationFrame(updateConstellation);
  }

  if (constellationContainer) {
      updateConstellation();
  }
});